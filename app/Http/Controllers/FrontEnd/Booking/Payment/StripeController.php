<?php

namespace App\Http\Controllers\FrontEnd\Booking\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Booking\ServicePaymentController;
use App\Http\Helpers\CheckLimitHelper;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffGlobalHour;
use App\Models\Staff\StaffServiceHour;
use Cartalyst\Stripe\Stripe as CartalystStripe;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Response;

class StripeController extends Controller
{
  public function index(Request $request)
  {
    Log::info('Stripe service booking payment started', [
      'host' => $request->getHost(),
      'session_id' => $request->session()->getId(),
      'has_service_data' => Session::has('serviceData'),
      'booking_date' => $request->input('bookingDate'),
      'service_hour_id' => $request->input('serviceHourId'),
      'staff_id' => $request->input('staffId'),
      'user_id' => $request->input('user_id'),
      'has_stripe_token' => $request->filled('stripeToken'),
    ]);

    if (Session::has('serviceData')) {
      $serviceData = Session::get('serviceData');
      Log::info('Stripe serviceData loaded from session', [
        'session_id' => $request->session()->getId(),
        'service_id' => $serviceData['service_id'] ?? null,
        'vendor_id' => $serviceData['vendor_id'] ?? null,
        'service_amount' => $serviceData['service_ammount'] ?? null,
        'has_question_answers' => !empty($serviceData['question_answers'] ?? null),
      ]);
    } else {
      Log::warning('Stripe payment aborted because serviceData session is missing', [
        'session_id' => $request->session()->getId(),
      ]);
      return $this->paymentErrorResponse($request, 'Your booking session expired. Please reopen the booking form and try again.', true);
    }

    // Tenant-deferred bookings intentionally have no staff/date/time chosen
    // yet — that gets picked later via the secure tenant link. Skip the
    // staff/slot resolution below for them; storeCartItems() overwrites
    // these fields per item regardless of what's built here.
    $isTenantDeferred = ($serviceData['scheduling_mode'] ?? 'customer') === 'tenant';
    
    $service = \App\Models\Services\Services::find($serviceData['service_id'] ?? null);
    $vendorId = $service ? (int) ($service->vendor_id ?? 0) : 0;
    $serviceData['vendor_id'] = $vendorId;

    Log::info('Stripe service ownership resolved', [
      'session_id' => $request->session()->getId(),
      'service_id' => $serviceData['service_id'] ?? null,
      'service_found' => (bool) $service,
      'vendor_id' => $vendorId,
      'is_admin_service' => $vendorId === 0,
    ]);
    
    // Get service_ammount from session or database
    if (!isset($serviceData['service_ammount']) || empty($serviceData['service_ammount'])) {
      $serviceData['service_ammount'] = $service ? $service->price : 0;

      Log::info('Stripe service amount resolved from service record', [
        'session_id' => $request->session()->getId(),
        'service_id' => $serviceData['service_id'] ?? null,
        'service_found' => (bool) $service,
        'service_amount' => $serviceData['service_ammount'],
      ]);
    }
    
    //check membership expire date
    $expireDate = $vendorId == 0 ? null : checkMembersipExpireDate($vendorId);
    Log::info('Stripe membership expiry precheck completed', [
      'session_id' => $request->session()->getId(),
      'vendor_id' => $vendorId,
      'booking_date' => $request['bookingDate'],
      'expire_date' => $expireDate,
      'will_block' => $vendorId != 0 && (empty($expireDate) || $request['bookingDate'] > $expireDate),
    ]);

    if ($vendorId != 0 && empty($expireDate)) {
      Log::warning('Stripe payment blocked because vendor has no active membership expiry date', [
        'session_id' => $request->session()->getId(),
        'vendor_id' => $vendorId,
        'booking_date' => $request['bookingDate'],
      ]);

      return $this->paymentErrorResponse($request, 'Vendor membership is not active. Please contact support.');
    }

    if ($vendorId != 0 && $request['bookingDate'] > $expireDate) {
      Log::warning('Stripe payment blocked by membership expiry precheck', [
        'session_id' => $request->session()->getId(),
        'vendor_id' => $vendorId,
        'booking_date' => $request['bookingDate'],
        'expire_date' => $expireDate,
      ]);

      return $this->paymentErrorResponse($request, 'Vendor membership has expired. Please contact support.');
    }
    
    $countAppointment = CheckLimitHelper::countAppointment($vendorId);
    Log::info('Stripe appointment limit precheck completed', [
      'session_id' => $request->session()->getId(),
      'vendor_id' => $vendorId,
      'remaining_appointments' => $countAppointment,
      'will_block' => $countAppointment <= 0,
    ]);

    if ($countAppointment > 0) {
      // card validation start
      $rules = [
        'stripeToken' => 'required_without:payment_intent_id',
        'payment_intent_id' => 'required_without:stripeToken|nullable|string',
      ];

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        Log::warning('Stripe payment validation failed', [
          'session_id' => $request->session()->getId(),
          'errors' => $validator->errors()->toArray(),
        ]);

        if (!$request->expectsJson()) {
          return redirect()->back()->with('error', 'Card payment could not be started. Please re-enter your card details.');
        }

        return Response::json(['errors' => $validator->errors()], 422);
      }

      Log::info('Stripe token validation passed', [
        'session_id' => $request->session()->getId(),
        'has_stripe_token' => $request->filled('stripeToken'),
      ]);
      // card validation end
      $customerpaid = round((float) $serviceData['service_ammount'], 2);

      if ($request->filled('total_price')) {
        $checkoutTotal = round((float) $request->input('total_price'), 2);

        if (abs($checkoutTotal - $customerpaid) > 0.01) {
          Log::warning('Stripe payment blocked because checkout total does not match server total', [
            'session_id' => $request->session()->getId(),
            'checkout_total' => $checkoutTotal,
            'server_total' => $customerpaid,
            'service_id' => $serviceData['service_id'] ?? null,
          ]);

          return $this->paymentErrorResponse($request, 'Payment total changed before charging your card. Please refresh the checkout and try again.');
        }
      }

      $currencyInfo = $this->getCurrencyInfo();
      $stripeTotal = $this->cartalystStripeAmount($customerpaid, $currencyInfo->base_currency_text);

      Log::info('Stripe amount and currency resolved', [
        'session_id' => $request->session()->getId(),
        'customer_paid' => $customerpaid,
        'base_currency' => $currencyInfo->base_currency_text,
        'cartalyst_amount' => $stripeTotal,
      ]);

      if ($customerpaid <= 0) {
        Log::warning('Stripe payment blocked because resolved amount is below Stripe minimum', [
          'session_id' => $request->session()->getId(),
          'customer_paid' => $customerpaid,
          'base_currency' => $currencyInfo->base_currency_text,
          'cartalyst_amount' => $stripeTotal,
        ]);

        Session::flash('error', 'The booking total must be greater than zero before card payment.');
        Session::forget('serviceData');
        return redirect()->route('frontend.service_booking.cancel');
      }

      $staff = Staff::find($request['staffId']);
      $requestedStaffFound = (bool) $staff;
      
      // Safety check: if staff doesn't exist, get the first staff member for this service owner.
      if (!$staff) {
        $staff = Staff::where('vendor_id', $vendorId)->first();
      }

      Log::info('Stripe staff lookup completed', [
        'session_id' => $request->session()->getId(),
        'requested_staff_id' => $request['staffId'],
        'requested_staff_found' => $requestedStaffFound,
        'resolved_staff_id' => $staff ? $staff->id : null,
        'resolved_staff_vendor_id' => $staff ? $staff->vendor_id : null,
        'resolved_staff_is_day' => $staff ? $staff->is_day : null,
      ]);
      
      if (!$staff && !$isTenantDeferred) {
        Log::warning('Stripe payment blocked because staff could not be resolved', [
          'session_id' => $request->session()->getId(),
          'requested_staff_id' => $request['staffId'],
          'vendor_id' => $vendorId,
        ]);

        return $this->paymentErrorResponse($request, 'Staff information is not available. Please choose another slot.');
      }

      if ($isTenantDeferred) {
        $staffHour = new \stdClass();
        $staffHour->start_time = '00:00:00';
        $staffHour->end_time = '00:00:00';

        Log::info('Stripe skipped slot resolution for a tenant-deferred booking', [
          'session_id' => $request->session()->getId(),
        ]);
      } elseif (strpos($request['serviceHourId'], 'default_') === 0) {
        $timeId = str_replace('default_', '', $request['serviceHourId']);
        $startTime = substr($timeId, 0, 2) . ':' . substr($timeId, 2, 2) . ':' . substr($timeId, 4, 2);
        
        $staffHour = new \stdClass();
        $staffHour->start_time = $startTime;
        $staffHour->end_time = \Carbon\Carbon::parse($startTime)->addHours(2)->format('H:i:s');

        Log::info('Stripe default time slot resolved', [
          'session_id' => $request->session()->getId(),
          'service_hour_id' => $request['serviceHourId'],
          'start_time' => $staffHour->start_time,
          'end_time' => $staffHour->end_time,
        ]);
      } else {
        if ($staff->is_day == 1) {
          $staffHour = StaffServiceHour::find($request['serviceHourId']);
          $timeSlotSource = 'staff_service_hours';
        } else {
          $staffHour = StaffGlobalHour::find($request['serviceHourId']);
          $timeSlotSource = 'staff_global_hours';
        }

        Log::info('Stripe database time slot lookup completed', [
          'session_id' => $request->session()->getId(),
          'service_hour_id' => $request['serviceHourId'],
          'source' => $timeSlotSource,
          'slot_found' => (bool) $staffHour,
          'start_time' => $staffHour ? $staffHour->start_time : null,
          'end_time' => $staffHour ? $staffHour->end_time : null,
        ]);
      }

      if (!$staffHour) {
        Log::warning('Stripe payment blocked because selected time slot could not be resolved', [
          'session_id' => $request->session()->getId(),
          'service_hour_id' => $request['serviceHourId'],
          'staff_id' => $request['staffId'],
          'staff_is_day' => $staff ? $staff->is_day : null,
        ]);

        return $this->paymentErrorResponse($request, 'Selected time slot is no longer available. Please choose another slot.');
      }

      $arrData = array(
        'zoom_status' => $serviceData['zoom_status'] ?? 0,
        'calender_status' => $serviceData['calendar_status'] ?? 0,
        'customer_name' => $request['name'],
        'customer_phone' => $request['phone'],
        'customer_email' => $request['email'],
        'customer_address' => $request['address'],
        'customer_zip_code' => $request['zip_code'],
        'customer_country' => $request['country'],
        'start_date' => $staffHour->start_time,
        'end_date' => $staffHour->end_time,
        'booking_date' => $request['bookingDate'],
        'service_hour_id' => $request['serviceHourId'],
        'staff_id' => $request['staffId'],
        'max_person' => $request['max_person'],
        'service_id' => $serviceData['service_id'],
        'user_id' => $request['user_id'],
        'vendor_id' => $vendorId,
        'customer_paid' => $customerpaid,
        'currencyText' => $currencyInfo->base_currency_text,
        'currencyTextPosition' => $currencyInfo->base_currency_text_position,
        'currencySymbol' => $currencyInfo->base_currency_symbol,
        'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
        'paymentMethod' => 'Stripe',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
        'orderStatus' => 'pending',
        'refund' => 'pending',
        'question_answers' => $serviceData['question_answers'] ?? null
      );

      Log::info('Stripe booking payload prepared before charge', [
        'session_id' => $request->session()->getId(),
        'service_id' => $arrData['service_id'],
        'vendor_id' => $arrData['vendor_id'],
        'staff_id' => $arrData['staff_id'],
        'service_hour_id' => $arrData['service_hour_id'],
        'booking_date' => $arrData['booking_date'],
        'start_date' => $arrData['start_date'],
        'end_date' => $arrData['end_date'],
        'customer_paid' => $arrData['customer_paid'],
        'payment_method' => $arrData['paymentMethod'],
      ]);

      try {
        $stripeGateway = OnlineGateway::where('keyword', 'stripe')->first();
        $stripeInfo = $stripeGateway ? json_decode($stripeGateway->information, true) : [];
        $stripeSecret = $stripeInfo['secret'] ?? null;

        Log::info('Stripe gateway configuration lookup completed', [
          'session_id' => $request->session()->getId(),
          'gateway_found' => (bool) $stripeGateway,
          'gateway_status' => $stripeGateway ? $stripeGateway->status : null,
          'secret_present' => !empty($stripeSecret),
        ]);

        if (empty($stripeSecret)) {
          Log::warning('Stripe secret key missing from gateway configuration', [
            'session_id' => $request->session()->getId(),
          ]);
          Session::flash('error', 'Stripe secret key is not configured.');
          Session::forget('serviceData');
          return redirect()->route('frontend.service_booking.cancel');
        }

        // Initialize Stripe from the payment gateway record so frontend and backend
        // use the same gateway configuration source.
        $stripe = new Stripe();
        Log::info('Stripe client initialization starting', [
          'session_id' => $request->session()->getId(),
        ]);

        $stripe = Stripe::make($stripeSecret);

        Log::info('Stripe client initialization completed', [
          'session_id' => $request->session()->getId(),
        ]);

        $chargeSucceeded = false;
        $paymentIntentId = $request->input('payment_intent_id');

        // Apple Pay / Google Pay already charged the card client-side via
        // stripe.confirmPayment() before this request arrived — verify that
        // PaymentIntent server-side (status/amount/currency/ownership) and
        // finalize the booking, instead of trying to charge again.
        if ($paymentIntentId) {
          $intent = $this->verifiedPaymentIntent($stripe, $paymentIntentId, $customerpaid, $currencyInfo->base_currency_text, $request->session()->getId());

          return $this->withPaymentIntentLock($paymentIntentId, function () use ($request, $arrData, $intent) {
            $paymentIntentId = $intent['id'];

            if (\App\Models\Services\ServiceBooking::where('payment_intent_id', $paymentIntentId)->exists()) {
              Log::warning('Stripe PaymentIntent finalize blocked because booking already exists', [
                'session_id' => $request->session()->getId(),
                'payment_intent_id' => $paymentIntentId,
              ]);

              return Response::json(['error' => 'This booking has already been placed.'], 409);
            }

            return $this->finalizeSuccessfulPayment($request, $arrData, $paymentIntentId);
          });
        }

        try {

          // generate charge
          Log::info('Stripe charge creation starting', [
            'session_id' => $request->session()->getId(),
            'currency' => $currencyInfo->base_currency_text,
            'cartalyst_amount' => $stripeTotal,
            'has_stripe_token' => $request->filled('stripeToken'),
          ]);

          $charge = $stripe->charges()->create([
            'source' => $request->stripeToken,
            'currency' => strtolower($currencyInfo->base_currency_text),
            'amount'   => $stripeTotal,
          ]);

          Log::info('Stripe charge response received', [
            'session_id' => $request->session()->getId(),
            'status' => $charge['status'] ?? null,
            'charge_id' => $charge['id'] ?? null,
            'cartalyst_amount' => $stripeTotal,
          ]);

          if ($charge['status'] == 'succeeded') {
            $chargeSucceeded = true;
            $bookingProcess = new ServicePaymentController();

            Log::info('Stripe charge succeeded, finalizing booking order', [
              'session_id' => $request->session()->getId(),
              'charge_id' => $charge['id'] ?? null,
            ]);

            $bookings = $bookingProcess->finalizeOrder($arrData);

            Log::info('Stripe booking order finalized, sending payment emails', [
              'session_id' => $request->session()->getId(),
              'booking_ids' => $bookings->pluck('id')->all(),
            ]);

            // The booking is already safely created and paid at this point —
            // a confirmation email failing (missing mail template, SMTP
            // misconfigured, etc.) must never make a successful booking
            // report back to the customer as "could not be completed".
            $bookings->each(function ($booking) {
              try {
                payemntStatusMail('service_payment_approved', $booking->id);
              } catch (Exception $e) {
                Log::error('Stripe payment confirmation email failed, but the booking was created successfully', [
                  'booking_id' => $booking->id,
                  'message' => $e->getMessage(),
                ]);
              }
            });

            Session::put('complete', 'payment_complete');
            Session::put('paymentInfo', $bookings->first());
            $request->session()->forget('serviceData');

            Log::info('Stripe payment succeeded and success session stored', [
              'session_id' => $request->session()->getId(),
              'booking_ids' => $bookings->pluck('id')->all(),
              'complete' => Session::get('complete'),
              'payment_info_id' => optional(Session::get('paymentInfo'))->id,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
              return Response::json(['redirectURL' => route('frontend.services')]);
            }

            return redirect()->route('frontend.services');
          } else {
            Log::warning('Stripe charge did not succeed', [
              'session_id' => $request->session()->getId(),
              'status' => $charge['status'] ?? null,
            ]);
            return $this->paymentErrorResponse($request, 'Card payment was not completed. Please try again or use another payment method.');
          }
        } catch (Exception $e) {
          Log::error('Stripe charge execution failed', [
            'session_id' => $request->session()->getId(),
            'message' => $e->getMessage(),
          ]);
          $message = $chargeSucceeded
            ? 'Payment was received, but the booking could not be completed. Please contact support before trying again.'
            : $this->stripeCustomerMessage($e);

          return $this->paymentErrorResponse($request, $message);
        }
      } catch (Exception $e) {
        Log::error('Stripe gateway initialization failed', [
          'session_id' => $request->session()->getId(),
          'message' => $e->getMessage(),
        ]);
        return $this->paymentErrorResponse($request, 'Card payment is temporarily unavailable. Please try another payment method or contact support.');
      }
    } else {
      Log::warning('Stripe payment blocked by appointment limit', [
        'session_id' => $request->session()->getId(),
      ]);
      return $this->paymentErrorResponse($request, 'Please contact support.');
    }
  }

  private function cartalystStripeAmount(float $amount, string $currency)
  {
    $zeroDecimalCurrencies = [
      'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG',
      'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
    ];

    return in_array(strtoupper($currency), $zeroDecimalCurrencies, true)
      ? (int) round($amount)
      : round($amount, 2);
  }

  // Apple Pay / Google Pay via Stripe's Express Checkout Element. Card
  // payment above is unaffected — this is an additional gateway value
  // ('stripe_intent') dispatched from ServicePaymentController that only
  // creates/reuses a PaymentIntent; the booking itself is only created once
  // the wallet payment is confirmed and this same endpoint is hit again
  // with gateway=stripe and a payment_intent_id instead of a stripeToken.
  public function createPaymentIntent(Request $request)
  {
    if (!Session::has('serviceData')) {
      return Response::json(['error' => 'Your booking session expired. Please reopen the booking form and try again.'], 422);
    }

    $serviceData = Session::get('serviceData');
    $customerpaid = round((float) ($serviceData['service_ammount'] ?? 0), 2);
    $currencyInfo = $this->getCurrencyInfo();

    if ($customerpaid <= 0) {
      return Response::json(['error' => 'The booking total must be greater than zero before card payment.'], 422);
    }

    if ($request->filled('total_price')) {
      $checkoutTotal = round((float) $request->input('total_price'), 2);

      if (abs($checkoutTotal - $customerpaid) > 0.01) {
        return Response::json(['error' => 'Payment total changed before charging your card. Please refresh the checkout and try again.'], 422);
      }
    }

    $stripeGateway = OnlineGateway::where('keyword', 'stripe')->first();
    $stripeInfo = $stripeGateway ? json_decode($stripeGateway->information, true) : [];
    $stripeSecret = $stripeInfo['secret'] ?? null;
    $stripeKey = $stripeInfo['key'] ?? null;

    if (!$stripeGateway || !$stripeGateway->status || empty($stripeSecret) || empty($stripeKey)) {
      return Response::json(['error' => 'Card payment is temporarily unavailable. Please try again later.'], 503);
    }
    
    // Keep the validated checkout request in the Laravel session.
    // This allows the same booking to be completed after bank authentication.
    // Never store Stripe tokens or card data here.
    Session::put(
      'stripe_service_return_payload',
      $request->except([
        '_token',
        'stripeToken',
        'payment_intent_id',
      ])
    );

    $amountMinor = $this->chargedMinorUnits($customerpaid, $currencyInfo->base_currency_text);
    $currency = strtolower($currencyInfo->base_currency_text);
    $stripe = Stripe::make($stripeSecret);
    $sessionIntent = Session::get('stripe_service_payment_intent');

    if (
      is_array($sessionIntent)
      && ($sessionIntent['amount'] ?? null) === $amountMinor
      && ($sessionIntent['currency'] ?? null) === $currency
      && !empty($sessionIntent['payment_intent_id'])
      && !empty($sessionIntent['client_secret'])
    ) {
      try {
        $existingIntent = $stripe->paymentIntents()->find($sessionIntent['payment_intent_id']);
        if (!in_array((string) ($existingIntent['status'] ?? ''), ['succeeded', 'canceled'], true)) {
          return Response::json([
            'client_secret' => $sessionIntent['client_secret'],
            'payment_intent_id' => $sessionIntent['payment_intent_id'],
            'publishable_key' => $stripeKey,
            'amount' => $amountMinor,
            'currency' => $currency,
          ]);
        }
      } catch (Exception $e) {
        Session::forget('stripe_service_payment_intent');
      }
    }

    try {
      $intent = $this->createStripePaymentIntent($stripe, $customerpaid, $currencyInfo->base_currency_text, $request->session()->getId());
    } catch (Exception $e) {
      Log::error('Stripe PaymentIntent creation failed', [
        'session_id' => $request->session()->getId(),
        'message' => $e->getMessage(),
      ]);

      return Response::json(['error' => 'Could not start the payment. Please try again.'], 502);
    }

    Session::put('stripe_service_payment_intent', [
      'client_secret' => $intent['client_secret'],
      'payment_intent_id' => $intent['id'],
      'amount' => $amountMinor,
      'currency' => $currency,
    ]);

    return Response::json([
      'client_secret' => $intent['client_secret'],
      'payment_intent_id' => $intent['id'],
      'publishable_key' => $stripeKey,
      'amount' => $amountMinor,
      'currency' => $currency,
    ]);
  }

  private function chargedMinorUnits(float $amount, string $currency): int
  {
    $zeroDecimalCurrencies = [
      'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG',
      'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
    ];

    return in_array(strtoupper($currency), $zeroDecimalCurrencies, true)
      ? (int) round($amount)
      : (int) round($amount * 100);
  }

  private function paymentIntentCreatePayload(float $amount, string $currency, string $sessionId): array
  {
    return [
      'amount' => $this->chargedMinorUnits($amount, $currency),
      'currency' => strtolower($currency),
      'automatic_payment_methods[enabled]' => 'true',
      'metadata' => [
        'source' => 'web_service_checkout',
        'session_id' => $sessionId,
      ],
    ];
  }

  private function createStripePaymentIntent($stripe, float $amount, string $currency, string $sessionId): array
  {
    $previousAmountConverter = CartalystStripe::getAmountConverter();
    CartalystStripe::disableAmountConverter();

    try {
      return $stripe->paymentIntents()->create(
        $this->paymentIntentCreatePayload($amount, $currency, $sessionId)
      );
    } finally {
      CartalystStripe::setAmountConverter($previousAmountConverter);
    }
  }

  private function verifiedPaymentIntent($stripe, string $paymentIntentId, float $amount, string $currency, string $sessionId): array
  {
    try {
      $intent = $stripe->paymentIntents()->find($paymentIntentId);
    } catch (Exception $e) {
      throw new Exception('Could not verify the payment.');
    }

    if (
      ($intent['status'] ?? null) !== 'succeeded'
      || (int) ($intent['amount'] ?? 0) !== $this->chargedMinorUnits($amount, $currency)
      || strtoupper((string) ($intent['currency'] ?? '')) !== strtoupper($currency)
      || (string) data_get($intent, 'metadata.source') !== 'web_service_checkout'
      || (string) data_get($intent, 'metadata.session_id') !== $sessionId
    ) {
      throw new Exception('Payment could not be verified. You have not been charged for a booking.');
    }

    return $intent;
  }

  private function withPaymentIntentLock(string $paymentIntentId, callable $callback)
  {
    $lock = Cache::lock('stripe-service-payment-intent:' . $paymentIntentId, 30);

    try {
      return $lock->block(5, $callback);
    } catch (Exception $e) {
      return Response::json(['error' => 'This payment is already being finalized. Please wait a moment.'], 409);
    }
  }

  private function finalizeSuccessfulPayment(Request $request, array $arrData, ?string $paymentIntentId = null)
  {
    Log::info('Stripe payment succeeded, finalizing booking order', [
      'session_id' => $request->session()->getId(),
      'payment_intent_id' => $paymentIntentId,
    ]);

    $bookings = DB::transaction(function () use ($arrData, $paymentIntentId) {
      $bookings = $this->makeServicePaymentController()->finalizeOrder($arrData);

      if ($paymentIntentId) {
        $bookings->each(function ($booking) use ($paymentIntentId) {
          \App\Models\Services\ServiceBooking::whereKey($booking->id)->update(['payment_intent_id' => $paymentIntentId]);
          $booking->payment_intent_id = $paymentIntentId;
        });
      }

      return $bookings;
    });

    Log::info('Stripe booking order finalized, sending payment emails', [
      'session_id' => $request->session()->getId(),
      'booking_ids' => $bookings->pluck('id')->all(),
      'payment_intent_id' => $paymentIntentId,
    ]);

    $bookings->each(function ($booking) {
      try {
        payemntStatusMail('service_payment_approved', $booking->id);
      } catch (Exception $e) {
        Log::error('Stripe payment confirmation email failed, but the booking was created successfully', [
          'booking_id' => $booking->id,
          'message' => $e->getMessage(),
        ]);
      }
    });

    Session::put('complete', 'payment_complete');
    Session::put('paymentInfo', $bookings->first());
    $request->session()->forget('serviceData');
    $request->session()->forget('stripe_service_payment_intent');
    $request->session()->forget('stripe_service_return_payload');

    if ($request->expectsJson() || $request->ajax()) {
      return Response::json(['redirectURL' => route('frontend.services')]);
    }

    return redirect()->route('frontend.services');
  }

  protected function makeServicePaymentController(): ServicePaymentController
  {
    return new ServicePaymentController();
  }

  private function paymentErrorResponse(Request $request, string $message, bool $clearSession = false)
  {
    if ($request->expectsJson() || $request->ajax()) {
      return Response::json(['error' => $message], 422);
    }

    Session::flash('error', $message);
    if ($clearSession) {
      Session::forget('serviceData');
    }

    return redirect()->route('frontend.service_booking.cancel');
  }

  private function stripeCustomerMessage(Exception $exception): string
  {
    $message = trim($exception->getMessage());

    if ($message === '') {
      return 'Card payment failed. Please check your card details or try another payment method.';
    }

    $safeFragments = [
      'card was declined',
      'incorrect number',
      'invalid number',
      'incorrect cvc',
      'invalid cvc',
      'expired',
      'insufficient funds',
      'authentication',
      'postal code',
      'zip code',
    ];

    foreach ($safeFragments as $fragment) {
      if (stripos($message, $fragment) !== false) {
        return $message;
      }
    }

    return 'Card payment failed. Please check your card details or try another payment method.';
  }

  /**
   * Display the payment verification page after bank authentication.
   * This GET request never creates a booking.
   */
  public function paymentReturn(Request $request)
  {
    $paymentIntentId = $request->query('payment_intent');

    $pending = Session::get('stripe_service_payment_intent');

    if (
      !is_string($paymentIntentId) ||
      !preg_match('/^pi_[A-Za-z0-9]+$/', $paymentIntentId) ||
      !is_array($pending) ||
      !hash_equals(
        (string) ($pending['payment_intent_id'] ?? ''),
        $paymentIntentId
      ) ||
      !Session::has('stripe_service_return_payload') ||
      !Session::has('serviceData')
    ) {
      abort(403, 'Payment session unavailable. Please contact support before paying again.');
    }

    return response()
      ->view('frontend.services.payment-return', [
        'paymentIntentId' => $paymentIntentId,
      ])
      ->header('Cache-Control', 'no-store, private');
  }

  /**
   * Complete a returned payment using the existing verified
   * Stripe PaymentIntent and booking finalisation process.
   */
  public function finishReturnedPayment(Request $request)
  {
    $request->validate([
      'payment_intent_id' => [
        'required',
        'string',
        'regex:/^pi_[A-Za-z0-9]+$/',
      ],
    ]);

    $pending = Session::get('stripe_service_payment_intent');
    $payload = Session::get('stripe_service_return_payload');

    if (
      !is_array($pending) ||
      !is_array($payload) ||
      !Session::has('serviceData') ||
      !hash_equals(
        (string) ($pending['payment_intent_id'] ?? ''),
        (string) $request->input('payment_intent_id')
      )
    ) {
      return Response::json([
        'error' => 'Unable to restore this payment. Contact support before paying again.',
      ], 409);
    }

    $paymentIntentId = $request->input('payment_intent_id');

    // Restore the original checkout data from the server session.
    // Never trust booking amounts or customer details from the return URL.
    $request->merge($payload);

    $request->merge([
      'gateway' => 'stripe',
      'payment_intent_id' => $paymentIntentId,
    ]);

    // This existing method verifies the PaymentIntent directly
    // with Stripe, checks amount/currency/session, and finalises.
    return $this->index($request);
  }

}
