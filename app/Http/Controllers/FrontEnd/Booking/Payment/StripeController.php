<?php

namespace App\Http\Controllers\FrontEnd\Booking\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Booking\ServicePaymentController;
use App\Http\Helpers\CheckLimitHelper;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffGlobalHour;
use App\Models\Staff\StaffServiceHour;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Exception;
use Illuminate\Http\Request;
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
        'stripeToken' => 'required',
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
      
      if (!$staff) {
        Log::warning('Stripe payment blocked because staff could not be resolved', [
          'session_id' => $request->session()->getId(),
          'requested_staff_id' => $request['staffId'],
          'vendor_id' => $vendorId,
        ]);

        return $this->paymentErrorResponse($request, 'Staff information is not available. Please choose another slot.');
      }

      if (strpos($request['serviceHourId'], 'default_') === 0) {
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

            $bookings->each(function ($booking) {
              $type = 'service_payment_approved';
              payemntStatusMail($type, $booking->id);
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
}
