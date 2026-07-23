<?php

namespace App\Http\Controllers\FrontEnd\Booking;

use App\Http\Controllers\Admin\Transaction\TransactionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Booking\Payment\AuthorizenetController;
use App\Http\Controllers\FrontEnd\Booking\Payment\FlutterwaveController;
use App\Http\Controllers\FrontEnd\Booking\Payment\InstamojoController;
use App\Http\Controllers\FrontEnd\Booking\Payment\MercadoPagoController;
use App\Http\Controllers\FrontEnd\Booking\Payment\MollieController;
use App\Http\Controllers\FrontEnd\Booking\Payment\OfflineController;
use App\Http\Controllers\FrontEnd\Booking\Payment\PayPalController;
use App\Http\Controllers\FrontEnd\Booking\Payment\PaystackController;
use App\Http\Controllers\FrontEnd\Booking\Payment\PaytmController;
use App\Http\Controllers\FrontEnd\Booking\Payment\RazorpayController;
use App\Http\Controllers\FrontEnd\Booking\Payment\StripeController;
use App\Http\Helpers\BasicMailer;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\Membership;
use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServicePackage;
use App\Models\Services\Services;
use App\Models\Shop\Coupon;
use App\Models\PostalCode;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Vendor;
use App\Models\BookingAnswer;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PDF;
use Validator;

class ServicePaymentController extends Controller
{
  public function index(Request $request)
  {
    Log::info('Service booking payment request received', [
      'host' => $request->getHost(),
      'session_id' => $request->session()->getId(),
      'gateway' => $request->input('gateway'),
      'cart_mode' => $request->boolean('cart_mode'),
      'has_service_data' => Session::has('serviceData'),
      'has_multi_service_order' => Session::has('multiServiceOrder'),
      'has_service_cart' => Session::has('serviceCart'),
      'service_id' => $request->input('service_id'),
      'booking_date' => $request->input('bookingDate'),
      'service_hour_id' => $request->input('serviceHourId'),
      'staff_id' => $request->input('staffId'),
      'user_id' => $request->input('user_id'),
      'total_price' => $request->input('total_price'),
    ]);

    //gateway validation
    $rules = ['gateway' => 'required',];
    $messages = ['gateway.required' => 'Please select a payment gateway'];
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      Log::warning('Service booking payment validation failed', [
        'session_id' => $request->session()->getId(),
        'errors' => $validator->errors()->toArray(),
      ]);
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $cartItems = collect(Session::get('serviceCart', []));
    $cartMode = $request->boolean('cart_mode') && $cartItems->isNotEmpty();
    $currencyInfo = $this->getCurrencyInfo();

    if ($cartMode) {
      if (!Auth::guard('web')->check()) {
        return response()->json([
          'error' => __('Please login before continuing to payment.'),
        ], 401);
      }

      $request->merge([
        'user_id' => Auth::guard('web')->id(),
      ]);

      if (trim((string) $request->input('zip_code')) === '') {
        return response()->json([
          'error' => __('Please enter your postcode and click Get price before continuing.'),
          'errors' => [
            'zip_code' => [__('Please enter your postcode and click Get price before continuing.')],
          ],
        ], 422);
      }

      $firstItem = $cartItems->first();
      $normalizedSlot = $this->normalizeCartTimeSlot($request->input('serviceHourId'));
      $questionAnswers = $this->decodeCartQuestionAnswers($request->input('question_answers', '{}'));
      $urgencySurcharge = (float) $request->input('urgency_surcharge_amount', 0);
      $urgencySurcharge = in_array($urgencySurcharge, [50.0, 100.0, 150.0], true) ? $urgencySurcharge : 0.0;
      $urgencyPeriod = $this->resolveCartUrgencyPeriod($urgencySurcharge);
      $isWithinFourHoursUrgency = $urgencySurcharge === 150.0;
      $bookingDate = $request->input('bookingDate');

      if ($isWithinFourHoursUrgency) {
        $bookingDate = null;
        $normalizedSlot = [];
        $request->merge([
          'bookingDate' => null,
          'serviceHourId' => null,
        ]);
      }

      try {
        $bookingDateIsPast = !empty($bookingDate) && Carbon::parse($bookingDate)->lt(Carbon::today());
      } catch (\Exception $exception) {
        $bookingDateIsPast = true;
      }

      if ($bookingDateIsPast || ($urgencySurcharge <= 0 && empty($bookingDate))) {
        return response()->json([
          'error' => __('Please choose today or a future booking date.'),
        ], 422);
      }

      $cartItems = $cartItems->map(function ($item) use ($request, $normalizedSlot, $questionAnswers, $isWithinFourHoursUrgency) {
        $item['booking_date'] = $isWithinFourHoursUrgency
          ? null
          : $request->input('bookingDate', $item['booking_date'] ?? null);
        $item['service_hour_id'] = $isWithinFourHoursUrgency
          ? null
          : ($normalizedSlot['service_hour_id'] ?? ($item['service_hour_id'] ?? null));
        $item['start_time'] = $isWithinFourHoursUrgency
          ? null
          : ($normalizedSlot['start_time'] ?? ($item['start_time'] ?? null));
        $item['end_time'] = $isWithinFourHoursUrgency
          ? null
          : ($normalizedSlot['end_time'] ?? ($item['end_time'] ?? null));
        $item['user_id'] = $request->input('user_id');

        if (isset($item['key'], $questionAnswers[$item['key']])) {
          $item['question_answers'] = json_encode($questionAnswers[$item['key']]);
        }

        return $item;
      });

      $quoteRequest = $request->boolean('quote_request') || $this->cartHasQuoteRequest($cartItems);
      $weekendRequest = $this->isWeekendBookingDate($bookingDate);
      $requestOnlyBooking = $quoteRequest || $weekendRequest;
      $cartPriceCheck = $this->resolveCartCheckoutPrice($cartItems, $urgencySurcharge, $requestOnlyBooking, $request->input('zip_code'));
      $clientTotal = round((float) $request->input('total_price', 0), 2);

      $couponDiscount = $this->resolveServiceCouponDiscount($cartPriceCheck['total']);
      $payableTotal = round(max($cartPriceCheck['total'] - $couponDiscount, 0), 2);

      if (!$cartPriceCheck['ok'] || (!$requestOnlyBooking && abs($clientTotal - $payableTotal) > 0.01)) {
        Log::warning('Cart payment price validation failed', [
          'session_id' => $request->session()->getId(),
          'client_total' => $clientTotal,
          'server_total' => $payableTotal,
          'cart_total' => $cartPriceCheck['total'],
          'discount' => $couponDiscount,
          'items' => $cartPriceCheck['items'],
          'urgency_surcharge' => $urgencySurcharge,
          'message' => $cartPriceCheck['message'],
        ]);

        return response()->json([
          'error' => $cartPriceCheck['message'],
          'server_total' => $payableTotal,
        ], 422);
      }

      $pricedItems = collect($cartPriceCheck['items'])->keyBy('key');
      $cartItems = $cartItems->map(function ($item) use ($pricedItems) {
        $pricedItem = $pricedItems->get($item['key'] ?? null);

        if ($pricedItem) {
          $item['price'] = $pricedItem['total'];
          $item['postcode_surcharge'] = $pricedItem['postcode_surcharge'] ?? 0.00;
          $item['postcode_rule'] = $pricedItem['postcode_rule'] ?? null;
        }

        return $item;
      });

      $firstItem = $cartItems->first();

      $orderData = [
        'cart_mode' => true,
        'items' => $cartItems->toArray(),
        'cart_total' => $cartPriceCheck['total'],
        'discount' => $couponDiscount,
        'coupon_code' => Session::get('serviceCouponCode'),
        'total' => $payableTotal,
        'urgency_surcharge_amount' => $urgencySurcharge,
        'urgency_surcharge_period' => $urgencyPeriod,
        'customer' => [
          'name' => $request->input('name'),
          'phone' => $request->input('phone'),
          'email' => $request->input('email'),
          'address' => $request->input('address'),
          'zip_code' => $request->input('zip_code'),
          'country' => $request->input('country'),
        ],
        'billing_same' => $request->input('billing_same', 1),
        'billing' => [
          'name' => $request->input('billing_name_secondary'),
          'phone' => $request->input('billing_phone_secondary'),
          'email' => $request->input('billing_email_secondary'),
          'address' => $request->input('billing_address_secondary'),
          'zip_code' => $request->input('billing_zip_secondary'),
          'country' => $request->input('billing_country_secondary'),
        ],
        'alternate' => [
          'name' => $request->input('billing_name_alt'),
          'phone' => $request->input('billing_phone_alt'),
          'email' => $request->input('billing_email_alt'),
          'address' => $request->input('billing_address_alt'),
          'zip_code' => $request->input('billing_zip_alt'),
          'country' => $request->input('billing_country_alt'),
        ],
        'currency' => [
          'text' => $currencyInfo->base_currency_text,
          'text_position' => $currencyInfo->base_currency_text_position,
          'symbol' => $currencyInfo->base_currency_symbol,
          'symbol_position' => $currencyInfo->base_currency_symbol_position,
        ],
      ];

      Session::put('multiServiceOrder', $orderData);

      if ($firstItem) {
        $request->merge([
          'bookingDate' => $firstItem['booking_date'],
          'serviceHourId' => $firstItem['service_hour_id'],
          'staffId' => $firstItem['staff_id'],
          'max_person' => $firstItem['max_person'],
          'service_id' => $firstItem['service_id'],
          'cart_mode' => 1,
        ]);
      }

      $serviceData = Session::get('serviceData', []);
      $serviceData['service_id'] = $firstItem['service_id'] ?? ($serviceData['service_id'] ?? null);
      $serviceData['vendor_id'] = (int) ($firstItem['vendor_id'] ?? ($serviceData['vendor_id'] ?? 0));
      $serviceData['service_ammount'] = $orderData['total'];
      $serviceData['urgency_surcharge_amount'] = $urgencySurcharge;
      $serviceData['urgency_surcharge_period'] = $urgencyPeriod;
      $serviceData['zoom_status'] = $firstItem['zoom_status'] ?? ($serviceData['zoom_status'] ?? 0);
      $serviceData['calendar_status'] = $firstItem['calendar_status'] ?? ($serviceData['calendar_status'] ?? 0);
      $serviceData['question_answers'] = $request->input('question_answers', $serviceData['question_answers'] ?? '{}');
      $serviceData['billing_same'] = $orderData['billing_same'];
      $serviceData['site_name'] = $orderData['customer']['name'] ?? null;
      $serviceData['site_phone'] = $orderData['customer']['phone'] ?? null;
      $serviceData['site_email'] = $orderData['customer']['email'] ?? null;
      $serviceData['site_address'] = $orderData['customer']['address'] ?? null;
      $serviceData['site_zip_code'] = $orderData['customer']['zip_code'] ?? null;
      $serviceData['site_country'] = $orderData['customer']['country'] ?? null;
      Session::put('serviceData', $serviceData);

      Log::info('Prepared multi-service order payload', [
        'session_id' => $request->session()->getId(),
        'items_count' => $cartItems->count(),
        'total' => $orderData['total'],
        'service_hour_id' => $firstItem['service_hour_id'] ?? null,
      ]);

      if ($quoteRequest) {
        return $this->storeWeekendCartRequest($request, $orderData, $firstItem, $currencyInfo, 'Booking Request');
      }

      if ($weekendRequest) {
        return $this->storeWeekendCartRequest($request, $orderData, $firstItem, $currencyInfo, 'Weekend Request');
      }
    } else {
      Session::forget('multiServiceOrder');
    }

    Log::info('Dispatching service booking payment gateway handler', [
      'session_id' => $request->session()->getId(),
      'gateway' => $request->input('gateway'),
      'service_data' => Session::get('serviceData'),
    ]);

    if ($request['gateway'] == 'paypal') {
      $paypal = new PayPalController();

      return $paypal->index($request, 'service booking');
    } else if ($request['gateway'] == 'instamojo') {
      $instamojo = new InstamojoController();

      return $instamojo->index($request, 'service booking');
    } else if ($request['gateway'] == 'paystack') {
      $paystack = new PaystackController();

      return $paystack->index($request, 'service booking');
    } else if ($request['gateway'] == 'flutterwave') {
      $flutterwave = new FlutterwaveController();

      return $flutterwave->index($request, 'service booking');
    } else if ($request['gateway'] == 'razorpay') {
      $razorpay = new RazorpayController();

      return $razorpay->index($request, 'service booking');
    } else if ($request['gateway'] == 'mercadopago') {
      $mercadopago = new MercadoPagoController();

      return $mercadopago->index($request, 'service booking');
    } else if ($request['gateway'] == 'mollie') {
      $mollie = new MollieController();

      return $mollie->index($request, 'service booking');
    } else if ($request['gateway'] == 'stripe') {
      $stripe = new StripeController();

      return $stripe->index($request, 'service booking');
    } else if ($request['gateway'] == 'paytm') {
      $paytm = new PaytmController();

      return $paytm->index($request, 'service booking');
    } else if ($request['gateway'] == 'authorize.net') {
      $authorize = new AuthorizenetController();

      return $authorize->index($request, 'service booking');
    } else {
      $offline = new OfflineController();

      return $offline->index($request, 'service booking');
    }
  }

  private function isWeekendBookingDate($date): bool
  {
    if (empty($date)) {
      return false;
    }

    try {
      return Carbon::parse($date)->isWeekend();
    } catch (\Exception $exception) {
      return false;
    }
  }

  private function storeWeekendCartRequest(Request $request, array $orderData, array $firstItem, $currencyInfo, string $paymentMethod = 'Weekend Request')
  {
    $orderData['items'] = collect($orderData['items'] ?? [])->map(function ($item) {
      $item['zoom_status'] = 0;
      $item['calendar_status'] = 0;

      return $item;
    })->toArray();

    $customer = $orderData['customer'] ?? [];

    $arrData = [
      'zoom_status' => 0,
      'calender_status' => 0,
      'customer_name' => $customer['name'] ?? $request->input('name'),
      'customer_phone' => $customer['phone'] ?? $request->input('phone'),
      'customer_email' => $customer['email'] ?? $request->input('email'),
      'customer_address' => $customer['address'] ?? $request->input('address'),
      'customer_zip_code' => $customer['zip_code'] ?? $request->input('zip_code'),
      'customer_country' => $customer['country'] ?? $request->input('country'),
      'start_date' => $firstItem['start_time'] ?? null,
      'end_date' => $firstItem['end_time'] ?? null,
      'booking_date' => $request->input('bookingDate'),
      'service_hour_id' => $firstItem['service_hour_id'] ?? null,
      'staff_id' => $firstItem['staff_id'] ?? null,
      'max_person' => $firstItem['max_person'] ?? 1,
      'service_id' => $firstItem['service_id'] ?? null,
      'user_id' => $request->input('user_id'),
      'vendor_id' => $firstItem['vendor_id'] ?? 0,
      'customer_paid' => $orderData['total'] ?? 0,
      'currencyText' => $currencyInfo->base_currency_text,
      'currencyTextPosition' => $currencyInfo->base_currency_text_position,
      'currencySymbol' => $currencyInfo->base_currency_symbol,
      'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
      'paymentMethod' => $paymentMethod,
      'gatewayType' => 'offline',
      'paymentStatus' => 'pending',
      'orderStatus' => 'pending',
      'refund' => 'pending',
      'attachment' => null,
      'question_answers' => $request->input('question_answers', '{}'),
      'billing_same' => $orderData['billing_same'] ?? 1,
    ];

    Session::put('multiServiceOrder', $orderData);

    $bookings = $this->finalizeOrder($arrData);

    Session::put('complete', 'payment_complete');
    Session::put('paymentInfo', $bookings->first());
    $request->session()->forget('serviceData');

    Log::info('Cart booking request stored without payment', [
      'session_id' => $request->session()->getId(),
      'booking_ids' => $bookings->pluck('id')->all(),
      'booking_date' => $request->input('bookingDate'),
      'payment_method' => $paymentMethod,
      'total' => $orderData['total'] ?? 0,
    ]);

    return response()->json(['redirectURL' => route('frontend.services')]);
  }

  private function normalizeCartTimeSlot($slot): array
  {
    $slot = trim((string) $slot);

    if ($slot === '') {
      return [];
    }

    if (Str::startsWith($slot, 'default_') || is_numeric($slot)) {
      return ['service_hour_id' => $slot];
    }

    $parts = preg_split('/\s*-\s*/', $slot);

    if (count($parts) !== 2) {
      return ['service_hour_id' => $slot];
    }

    try {
      $start = Carbon::parse($parts[0]);
      $end = Carbon::parse($parts[1]);

      return [
        'service_hour_id' => 'default_' . $start->format('His'),
        'start_time' => $start->format('H:i:s'),
        'end_time' => $end->format('H:i:s'),
      ];
    } catch (\Exception $exception) {
      Log::warning('Unable to normalize cart checkout time slot', [
        'slot' => $slot,
        'message' => $exception->getMessage(),
      ]);

      return ['service_hour_id' => $slot];
    }
  }

  private function resolveCartUrgencyPeriod(float $urgencySurcharge): string
  {
    if ($urgencySurcharge === 150.0) {
      return 'Within 4 hours';
    }

    if ($urgencySurcharge === 100.0) {
      return 'Within 24 hours';
    }

    if ($urgencySurcharge === 50.0) {
      return 'Within 48 hours';
    }

    return '';
  }

  private function decodeCartQuestionAnswers($answers): array
  {
    if (is_array($answers)) {
      return $answers;
    }

    if (!is_string($answers) || trim($answers) === '') {
      return [];
    }

    $decoded = json_decode($answers, true);

    return is_array($decoded) ? $decoded : [];
  }

  private function resolveCartCheckoutPrice(Collection $cartItems, float $urgencySurcharge, bool $allowRequestTotal = false, ?string $postcode = null): array
  {
    $serviceIds = $cartItems->filter(function ($item) {
      return ($item['item_type'] ?? 'service') === 'service';
    })->pluck('service_id')->filter()->unique()->values();
    $services = Services::query()->whereIn('id', $serviceIds)->get()->keyBy('id');
    $packageIds = $cartItems->filter(function ($item) {
      return ($item['item_type'] ?? 'service') === 'package';
    })->pluck('package_id')->filter()->unique()->values();
    $packages = ServicePackage::with('services')->whereIn('id', $packageIds)->get()->keyBy('id');
    $pricedItems = [];
    $total = 0.00;

    foreach ($cartItems as $item) {
      if (($item['item_type'] ?? 'service') === 'package') {
        $package = $packages->get($item['package_id'] ?? null);

        if (!$package || !$package->is_active) {
          return [
            'ok' => false,
            'total' => 0.00,
            'items' => $pricedItems,
            'message' => 'One of the packages in your cart is no longer available. Please refresh and try again.',
          ];
        }

        $answers = json_decode($item['question_answers'] ?? '{}', true);
        $answers = is_array($answers) ? $answers : [];
        $basePrice = $package->hasPricePlaceholder() ? 0.00 : $package->activePrice();
        $questionPrice = $this->calculatePackageQuestionPrice((int) $package->id, $answers);
        $postcodeSurcharge = $this->resolvePostcodeSurcharge($postcode, [
          'service_package_id' => (int) $package->id,
        ]);
        $itemTotal = round($basePrice + $questionPrice + $postcodeSurcharge['price'], 2);
        $pricedItems[] = [
          'key' => $item['key'] ?? null,
          'item_type' => 'package',
          'package_id' => $package->id,
          'base' => $basePrice,
          'questions' => $questionPrice,
          'postcode_surcharge' => $postcodeSurcharge['price'],
          'postcode_rule' => $postcodeSurcharge['rule'],
          'total' => $itemTotal,
        ];
        $total += $itemTotal;
        continue;
      }

      $service = $services->get($item['service_id'] ?? null);

      if (!$service) {
        return [
          'ok' => false,
          'total' => 0.00,
          'items' => $pricedItems,
          'message' => 'One of the services in your cart is no longer available. Please refresh and try again.',
        ];
      }

      $answers = json_decode($item['question_answers'] ?? '{}', true);
      $answers = is_array($answers) ? $answers : [];
      $basePrice = $this->resolveCartBasePrice($service, $item);
      $questionPrice = $this->calculateCartQuestionPrice((int) $service->id, $answers);
      $postcodeSurcharge = $this->resolvePostcodeSurcharge($postcode, [
        'service_id' => (int) $service->id,
        'service_category_id' => $this->serviceCategoryId((int) $service->id),
      ]);
      $itemTotal = round($basePrice + $questionPrice + $postcodeSurcharge['price'], 2);

      $pricedItems[] = [
        'key' => $item['key'] ?? null,
        'item_type' => 'service',
        'service_id' => $service->id,
        'base' => $basePrice,
        'questions' => $questionPrice,
        'postcode_surcharge' => $postcodeSurcharge['price'],
        'postcode_rule' => $postcodeSurcharge['rule'],
        'total' => $itemTotal,
      ];

      $total += $itemTotal;
    }

    $total = round($total + $urgencySurcharge, 2);

    return [
      'ok' => $total > 0 || $allowRequestTotal,
      'total' => $total,
      'items' => $pricedItems,
      'message' => $total > 0
        ? 'The price changed while you were booking. Please review the updated total and try again.'
        : 'The booking total must be greater than zero before payment.',
    ];
  }

  public function resolveServiceCartItemPrice(Services $service, array $answers, ?string $postcode = null, array $item = []): array
  {
    $basePrice = $this->resolveCartBasePrice($service, $item);
    $questionPrice = $this->calculateCartQuestionPrice((int) $service->id, $answers);
    $postcodeSurcharge = $this->resolvePostcodeSurcharge($postcode, [
      'service_id' => (int) $service->id,
      'service_category_id' => $this->serviceCategoryId((int) $service->id),
    ]);
    $total = round($basePrice + $questionPrice + $postcodeSurcharge['price'], 2);

    return [
      'base' => $basePrice,
      'questions' => $questionPrice,
      'postcode_surcharge' => $postcodeSurcharge['price'],
      'total' => $total,
    ];
  }

  private function resolveServiceCouponDiscount(float $total): float
  {
    $code = Session::get('serviceCouponCode');

    if (empty($code) || $total <= 0) {
      return 0.00;
    }

    $coupon = Coupon::where('code', $code)->first();

    if (!$coupon) {
      Session::forget('serviceCouponCode');
      Session::forget('serviceCouponDiscount');
      Session::forget('serviceCouponType');
      Session::forget('serviceCouponValue');

      return 0.00;
    }

    $todayDate = Carbon::now();

    if (!$todayDate->between(Carbon::parse($coupon->start_date), Carbon::parse($coupon->end_date))) {
      return 0.00;
    }

    if (!empty($coupon->minimum_spend) && $total < (float) $coupon->minimum_spend) {
      return 0.00;
    }

    $discount = $coupon->type == 'fixed'
      ? (float) $coupon->value
      : $total * ((float) $coupon->value / 100);

    $discount = round(min(max($discount, 0), $total), 2);
    Session::put('serviceCouponDiscount', $discount);
    Session::put('serviceCouponType', $coupon->type);
    Session::put('serviceCouponValue', (float) $coupon->value);

    return $discount;
  }

  private function resolvePostcodeSurcharge(?string $postcode, array $context): array
  {
    $surcharge = PostalCode::surchargeFor($postcode, $context);

    return [
      'price' => $surcharge ? round((float) $surcharge['price'], 2) : 0.00,
      'rule' => $surcharge,
    ];
  }

  private function serviceCategoryId(int $serviceId): ?int
  {
    return ServiceContent::query()
      ->where('service_id', $serviceId)
      ->value('category_id');
  }

  private function resolveCartBasePrice(Services $service, array $item): float
  {
    if ($service->hasPricePlaceholder()) {
      return 0.00;
    }

    $servicePrice = (float) ($service->price ?? 0);

    if ($servicePrice > 0) {
      return round($servicePrice, 2);
    }

    $itemPrice = (float) ($item['price'] ?? 0);

    if ($itemPrice > 0) {
      return round($itemPrice, 2);
    }

    $questions = Question::query()
      ->where('service_id', $service->id)
      ->orderBy('id')
      ->get();
    $optionGroups = QuestionOption::query()
      ->whereIn('question_id', $questions->pluck('id'))
      ->get()
      ->groupBy('question_id');

    foreach ($questions->where('is_required', true) as $question) {
      $requiredOptionPrice = $optionGroups
        ->get($question->id, collect())
        ->pluck('price')
        ->filter(function ($price) {
          return (float) $price > 0;
        })
        ->min();

      if ($requiredOptionPrice) {
        return round((float) $requiredOptionPrice, 2);
      }
    }

    $optionPrice = $optionGroups
      ->flatten(1)
      ->pluck('price')
      ->filter(function ($price) {
        return (float) $price > 0;
      })
      ->min();

    if ($optionPrice) {
      return round((float) $optionPrice, 2);
    }

    $quantityPrice = $questions
      ->where('type', 'quantity')
      ->map(function ($question) {
        $quantity = $question->quantity_default ?? $question->quantity_min ?? 1;
        return (float) $quantity * (float) ($question->quantity_unit_price ?? 0);
      })
      ->filter(function ($amount) {
        return $amount > 0;
      })
      ->min();

    if ($quantityPrice) {
      return round((float) $quantityPrice, 2);
    }

    return 0.00;
  }

  private function calculateCartQuestionPrice(int $serviceId, array $answers): float
  {
    return $this->calculateQuestionPriceForTarget('service_id', $serviceId, $answers);
  }

  private function calculatePackageQuestionPrice(int $packageId, array $answers): float
  {
    return $this->calculateQuestionPriceForTarget('service_package_id', $packageId, $answers);
  }

  private function calculateQuestionPriceForTarget(string $targetColumn, int $targetId, array $answers): float
  {
    $questions = Question::query()
      ->where($targetColumn, $targetId)
      ->get()
      ->keyBy('id');
    $questionOptions = QuestionOption::query()
      ->whereIn('question_id', $questions->keys())
      ->get()
      ->groupBy('question_id');

    $total = 0.00;

    foreach ($answers as $questionId => $answer) {
      $question = $questions->get((int) $questionId);

      if (!$question) {
        continue;
      }

      if ($question->type === 'quantity') {
        $quantity = (int) $answer;
        $default = $question->quantity_default !== null ? (int) $question->quantity_default : 0;
        $min = $question->quantity_min !== null ? (int) $question->quantity_min : null;
        $max = $question->quantity_max !== null ? (int) $question->quantity_max : null;

        if ($min !== null) {
          $quantity = max($min, $quantity);
        }

        if ($max !== null) {
          $quantity = min($max, $quantity);
        }

        if ($quantity > $default) {
          $total += $quantity * (float) $question->quantity_unit_price;
        }

        continue;
      }

      if (!in_array($question->type, ['select', 'multi'], true) || $answer === null || $answer === '') {
        continue;
      }

      $selectedValues = is_array($answer) ? $answer : [$answer];
      $selectedValues = array_map('strval', $selectedValues);

      foreach ($questionOptions->get($question->id, collect()) as $option) {
        if (
          in_array((string) $option->id, $selectedValues, true) ||
          in_array((string) $option->option_text, $selectedValues, true)
        ) {
          if ($this->isQuoteRequestOption($option)) {
            continue;
          }

          $total += (float) $option->price;
        }
      }
    }

    return round($total, 2);
  }

  private function cartHasQuoteRequest(Collection $cartItems): bool
  {
    $serviceIds = $cartItems
      ->filter(function ($item) {
        return ($item['item_type'] ?? 'service') === 'service';
      })
      ->pluck('service_id')
      ->filter()
      ->unique()
      ->values();
    $packageIds = $cartItems
      ->filter(function ($item) {
        return ($item['item_type'] ?? 'service') === 'package';
      })
      ->pluck('package_id')
      ->filter()
      ->unique()
      ->values();

    if ($serviceIds->isEmpty() && $packageIds->isEmpty()) {
      return false;
    }

    $questions = Question::query()
      ->whereIn('type', ['select', 'multi'])
      ->where(function ($query) use ($serviceIds, $packageIds) {
        if ($serviceIds->isNotEmpty()) {
          $query->whereIn('service_id', $serviceIds);
        }

        if ($packageIds->isNotEmpty()) {
          $method = $serviceIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
          $query->{$method}('service_package_id', $packageIds);
        }
      })
      ->get(['id']);

    if ($questions->isEmpty()) {
      return false;
    }

    $questionOptions = QuestionOption::query()
      ->whereIn('question_id', $questions->pluck('id'))
      ->get(['id', 'question_id', 'option_text']);

    foreach ($cartItems as $item) {
      $answers = json_decode($item['question_answers'] ?? '{}', true);
      $answers = is_array($answers) ? $answers : [];

      foreach ($answers as $questionId => $answer) {
        $selectedValues = is_array($answer) ? $answer : [$answer];
        $selectedValues = array_map('strval', $selectedValues);

        foreach ($questionOptions->where('question_id', (int) $questionId) as $option) {
          if (
            $this->isQuoteRequestOption($option) &&
            (in_array((string) $option->id, $selectedValues, true) ||
              in_array((string) $option->option_text, $selectedValues, true))
          ) {
            return true;
          }
        }
      }
    }

    return false;
  }

  private function isQuoteRequestOption(QuestionOption $option): bool
  {
    $optionText = Str::lower((string) $option->option_text);

    return Str::contains($optionText, '20+') && Str::contains($optionText, 'circuit');
  }

  public function finalizeOrder(array $arrData): Collection
  {
    $orderData = Session::get('multiServiceOrder');
    $cartItems = collect($orderData['items'] ?? []);

    Log::info('Finalizing service booking order', [
      'session_id' => session()->getId(),
      'cart_items_count' => $cartItems->count(),
      'service_id' => $arrData['service_id'] ?? null,
      'booking_date' => $arrData['booking_date'] ?? null,
      'payment_method' => $arrData['paymentMethod'] ?? null,
      'payment_status' => $arrData['paymentStatus'] ?? null,
    ]);

    if ($cartItems->isNotEmpty()) {
      return $this->storeCartItems($arrData, $orderData);
    }

    zoomCreate($arrData);
    calendarEventCreate($arrData);

    $booking = $this->storeData($arrData);

    return collect([$booking]);
  }

  protected function storeCartItems(array $arrData, array $orderData): Collection
  {
    return DB::transaction(function () use ($arrData, $orderData) {
      $items = collect($orderData['items'] ?? []);
      $groupToken = Str::uuid()->toString();
      $customer = $orderData['customer'] ?? [];
      $currency = $orderData['currency'] ?? [];
      $orders = collect();
      $itemIndex = 0;
      $remainingDiscount = round((float) ($orderData['discount'] ?? 0), 2);

      foreach ($items as $item) {
        $itemArr = $arrData;

        $itemArr['customer_name'] = $customer['name'] ?? $arrData['customer_name'];
        $itemArr['customer_phone'] = $customer['phone'] ?? $arrData['customer_phone'];
        $itemArr['customer_email'] = $customer['email'] ?? $arrData['customer_email'];
        $itemArr['customer_address'] = $customer['address'] ?? $arrData['customer_address'];
        $itemArr['customer_zip_code'] = $customer['zip_code'] ?? $arrData['customer_zip_code'];
        $itemArr['customer_country'] = $customer['country'] ?? $arrData['customer_country'];
        $site = $item['site'] ?? ($item['customer'] ?? []);
        $itemArr['billing_same'] = $item['billing_same'] ?? ($orderData['billing_same'] ?? ($arrData['billing_same'] ?? 1));
        $itemArr['site_name'] = $site['name'] ?? null;
        $itemArr['site_phone'] = $site['phone'] ?? null;
        $itemArr['site_email'] = $site['email'] ?? null;
        $itemArr['site_address'] = $site['address'] ?? null;
        $itemArr['site_zip_code'] = $site['zip_code'] ?? null;
        $itemArr['site_country'] = $site['country'] ?? null;

        $itemArr['item_type'] = $item['item_type'] ?? 'service';
        $itemArr['service_id'] = $item['service_id'];
        $itemArr['service_package_id'] = $item['package_id'] ?? null;
        $itemArr['package_name'] = $item['package_name'] ?? null;
        $itemArr['package_price'] = ($itemArr['item_type'] === 'package') ? ($item['price'] ?? null) : null;
        $itemArr['package_included_services'] = $item['included_services'] ?? null;
        $itemArr['booking_date'] = $item['booking_date'];
        $itemArr['start_date'] = $item['start_time'] ?? null;
        $itemArr['end_date'] = $item['end_time'] ?? null;
        $itemArr['service_hour_id'] = isset($item['service_hour_id']) && is_numeric($item['service_hour_id']) ? $item['service_hour_id'] : null;
        $itemArr['staff_id'] = $item['staff_id'] ?? ($arrData['staff_id'] ?? null);
        $itemArr['max_person'] = $item['max_person'] ?? ($arrData['max_person'] ?? 1);
        $itemSurcharge = $itemIndex === 0 ? (float) ($orderData['urgency_surcharge_amount'] ?? 0) : 0;
        $grossItemTotal = round($item['price'] + $itemSurcharge, 2);
        $itemDiscount = min($remainingDiscount, $grossItemTotal);
        $remainingDiscount = round(max($remainingDiscount - $itemDiscount, 0), 2);
        $itemArr['customer_paid'] = round(max($grossItemTotal - $itemDiscount, 0), 2);
        $itemArr['urgency_surcharge_amount'] = $itemSurcharge;
        $itemArr['urgency_surcharge_period'] = $itemSurcharge > 0 ? ($orderData['urgency_surcharge_period'] ?? null) : null;
        $itemArr['user_id'] = $item['user_id'] ?? ($arrData['user_id'] ?? null);
        $itemArr['vendor_id'] = $item['vendor_id'] ?? ($arrData['vendor_id'] ?? 0);
        $itemArr['zoom_status'] = $item['zoom_status'] ?? ($arrData['zoom_status'] ?? 0);
        $itemArr['calender_status'] = $item['calendar_status'] ?? ($arrData['calender_status'] ?? 0);
        $itemArr['group_token'] = $groupToken;
        $itemArr['question_answers'] = $item['question_answers'] ?? ($arrData['question_answers'] ?? null);

        if (!empty($currency)) {
          $itemArr['currencyText'] = $currency['text'] ?? $itemArr['currencyText'];
          $itemArr['currencyTextPosition'] = $currency['text_position'] ?? $itemArr['currencyTextPosition'];
          $itemArr['currencySymbol'] = $currency['symbol'] ?? $itemArr['currencySymbol'];
          $itemArr['currencySymbolPosition'] = $currency['symbol_position'] ?? $itemArr['currencySymbolPosition'];
        }

        zoomCreate($itemArr);
        calendarEventCreate($itemArr);

        $orders->push($this->storeData($itemArr));
        $itemIndex++;
      }

      Session::forget('serviceCart');
      Session::forget('multiServiceOrder');
      Session::forget('serviceCouponCode');
      Session::forget('serviceCouponDiscount');
      Session::forget('serviceCouponType');
      Session::forget('serviceCouponValue');

      Log::info('Stored cart-based service bookings', [
        'session_id' => session()->getId(),
        'group_token' => $groupToken,
        'orders_count' => $orders->count(),
      ]);

      return $orders;
    });
  }

  public function storeData($arrData)
  {
    $service = Services::find($arrData['service_id'] ?? null);
    if (($arrData['item_type'] ?? 'service') !== 'package') {
      $arrData['vendor_id'] = $service ? (int) ($service->vendor_id ?? 0) : (int) ($arrData['vendor_id'] ?? 0);
    } else {
      $arrData['vendor_id'] = (int) ($arrData['vendor_id'] ?? 0);
    }
    
    $currentPackage = $arrData['vendor_id'] != 0
      ? Membership::query()->where([
        ['vendor_id', '=', $arrData['vendor_id']],
        ['status', '=', 1],
        ['start_date', '<=', Carbon::now()->format('Y-m-d')],
        ['expire_date', '>=', Carbon::now()->format('Y-m-d')]
      ])->first()
      : null;

    $bookingYear = date('Y');
    $yearCount = ServiceBooking::whereYear('created_at', $bookingYear)->count();
    $orderNumber = 'SBK-' . $bookingYear . '-' . chr(65 + intdiv($yearCount, 100)) . str_pad($yearCount % 100, 2, '0', STR_PAD_LEFT);

    $orderInfo = ServiceBooking::create([
      'order_number' => $orderNumber,
      'group_token' => $arrData['group_token'] ?? null,
      'item_type' => $arrData['item_type'] ?? 'service',
      'membership_id' => $arrData['vendor_id'] != 0 ? ($currentPackage ? $currentPackage->id : null) : null,
      'customer_name' => $arrData['customer_name'],
      'customer_phone' => $arrData['customer_phone'],
      'customer_email' => $arrData['customer_email'],
      'customer_address' => $arrData['customer_address'],
      'customer_zip_code' => $arrData['customer_zip_code'],
      'customer_country' => $arrData['customer_country'],
      'start_date' => $arrData['start_date'],
      'end_date' => $arrData['end_date'],
      'booking_date' => $arrData['booking_date'],
      'staff_id' => $arrData['staff_id'],
      'service_id' => $arrData['service_id'],
      'service_package_id' => $arrData['service_package_id'] ?? null,
      'package_name' => $arrData['package_name'] ?? null,
      'package_price' => $arrData['package_price'] ?? null,
      'package_included_services' => $arrData['package_included_services'] ?? null,
      'max_person' => $arrData['max_person'] != null ? $arrData['max_person'] : 1,
      'user_id' => $arrData['user_id'],
      'vendor_id' => $arrData['vendor_id'],
      'service_hour_id' => (isset($arrData['service_hour_id']) && is_numeric($arrData['service_hour_id'])) ? $arrData['service_hour_id'] : null,
      'customer_paid' => $arrData['customer_paid'],
      'urgency_surcharge_amount' => $arrData['urgency_surcharge_amount'] ?? (Session::get('serviceData')['urgency_surcharge_amount'] ?? null),
      'urgency_surcharge_period' => $arrData['urgency_surcharge_period'] ?? (Session::get('serviceData')['urgency_surcharge_period'] ?? null),
      'billing_same' => $arrData['billing_same'] ?? (Session::get('serviceData')['billing_same'] ?? 1),
      'site_name'     => $arrData['site_name'] ?? (Session::get('serviceData')['site_name'] ?? null),
      'site_phone'    => $arrData['site_phone'] ?? (Session::get('serviceData')['site_phone'] ?? null),
      'site_email'    => $arrData['site_email'] ?? (Session::get('serviceData')['site_email'] ?? null),
      'site_address'  => $arrData['site_address'] ?? (Session::get('serviceData')['site_address'] ?? null),
      'site_zip_code' => $arrData['site_zip_code'] ?? (Session::get('serviceData')['site_zip_code'] ?? null),
      'site_country'  => $arrData['site_country'] ?? (Session::get('serviceData')['site_country'] ?? null),
      'currency_text' => $arrData['currencyText'],
      'currency_text_position' => $arrData['currencyTextPosition'],
      'currency_symbol' => $arrData['currencySymbol'],
      'currency_symbol_position' => $arrData['currencySymbolPosition'],
      'payment_method' => $arrData['paymentMethod'],
      'gateway_type' => $arrData['gatewayType'],
      'payment_status' => $arrData['paymentStatus'],
      'order_status' => $arrData['orderStatus'],
      'refund' => $arrData['refund'],
      'attachment' => array_key_exists('attachment', $arrData) ? $arrData['attachment'] : null,
      'zoom_info' => session()->has('zoom_info') ? json_encode(session()->get('zoom_info')) : null,
    ]);

    //create transaction for this payment
    if ($arrData['gatewayType'] != 'offline') {
      $transaction = new TransactionController();
      $transaction->storeTransaction($arrData);
    }
    
    if ($arrData['vendor_id'] != 0) {
      $vendor = Vendor::findOrFail($arrData['vendor_id']);
      $lessAppointmentNum = intval($vendor->total_appointment) - 1;
      //update less appoitnment number
      $vendor->update([
        'total_appointment' => $lessAppointmentNum,
      ]);
    }

    session::forget('zoom_info');

    $questionAnswers = $arrData['question_answers'] ?? null;
    $decodedAnswers = is_string($questionAnswers) ? json_decode($questionAnswers, true) : $questionAnswers;
    Log::info('Service booking question answers payload checked', [
      'session_id' => session()->getId(),
      'booking_id' => $orderInfo->id,
      'service_id' => $orderInfo->service_id,
      'has_question_answers' => is_array($decodedAnswers) && count($decodedAnswers) > 0,
      'question_answer_count' => is_array($decodedAnswers) ? count($decodedAnswers) : 0,
    ]);

    // Save booking answers
    if (is_array($decodedAnswers) && count($decodedAnswers) > 0) {
      $this->saveBookingAnswers($orderInfo->id, $arrData['question_answers']);
    }

    Log::info('Service booking stored successfully', [
      'session_id' => session()->getId(),
      'booking_id' => $orderInfo->id,
      'order_number' => $orderInfo->order_number,
      'service_id' => $orderInfo->service_id,
      'user_id' => $orderInfo->user_id,
      'vendor_id' => $orderInfo->vendor_id,
      'payment_method' => $orderInfo->payment_method,
      'payment_status' => $orderInfo->payment_status,
      'customer_paid' => $orderInfo->customer_paid,
    ]);

    return $orderInfo;
  }

  public function generateInvoice($orderInfo)
  {
    $fileName = $orderInfo->order_number . '.pdf';

    $data['orderInfo'] = $orderInfo;

    $directory = public_path('assets/file/invoices/service/');
    @mkdir($directory, 0775, true);

    $fileLocated = $directory . $fileName;

    PDF::loadView('frontend.services.invoice', $data)->save($fileLocated);

    return $fileName;
  }

  public function prepareMail($orderInfo)
  {
    // get the mail template info from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'service_booking_accepted')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    $appointment = ServiceBooking::where([
      'service_id' => $orderInfo['service_id'],
      'booking_date' => $orderInfo['booking_date'],
      'service_hour_id' => $orderInfo['service_hour_id']
    ])->select('zoom_info')->first();

    if ($appointment->zoom_info != null) {
      // Decode JSON data into an associative array
      $zoomLink = json_decode($appointment->zoom_info, true);
      $joinUrl = $zoomLink['join_url'];
      $joinPwd = $zoomLink['password'];
    }
    if ($appointment->zoom_info != null) {
      $joinurl = '<p>Zoom Join link: ' . $joinUrl . '</p>';
      $joinPassword = '<p>Zoom Join Password: ' . $joinPwd . '</p>';
    } else {
      $joinurl = '';
      $joinPassword = '';
    }

    if (Auth::guard('web')->check() == true) {
      $orderLink = '<p>Appointment Details: <a href=' . url("user/appointment/details/" . $orderInfo->id) . '>Click Here</a></p>';
    } else {
      $orderLink = '';
    }

    $language = Language::where('is_default', 1)->first();
    if (($orderInfo->item_type ?? 'service') === 'package') {
      $url = route('frontend.services');
      $serviceName = truncateString($orderInfo->package_name ?? 'Service Package', 50);
    } else {
      $serviceInfo = ServiceContent::query()
        ->where('service_id', $orderInfo->service_id)
        ->where('language_id', $language->id)
        ->select('name', 'slug')
        ->firstOrFail();

      $url = route('frontend.service.details', ['slug' => $serviceInfo->slug, 'id' => $orderInfo->service_id]);
      $serviceName = truncateString($serviceInfo->name, 50);
    }

    // get the website title info from db
    $info = Basic::select('website_title')->first();
    $appointmentTime = $orderInfo->start_date . ' to ' . $orderInfo->end_date;

    // replacing with actual data
    $mailBody = str_replace('{booking_number}', $orderInfo->order_number, $mailBody);
    $mailBody = str_replace('{service_title}', "<a href=" . $url . ">$serviceName</a>", $mailBody);
    $mailBody = str_replace('{order_link}', $orderLink, $mailBody);
    $mailBody = str_replace('{zoom_link}', $joinurl, $mailBody);
    $mailBody = str_replace('{zoom_password}', $joinPassword, $mailBody);
    $mailBody = str_replace('{customer_name}', $orderInfo->customer_name, $mailBody);
    $mailBody = str_replace('{booking_date}', date_format($orderInfo->created_at, 'M d, Y'), $mailBody);
    $mailBody = str_replace('{appointment_date}', Carbon::parse($orderInfo->booking_date)->format('M d, Y'), $mailBody);
    $mailBody = str_replace('{appointment_time}', $appointmentTime, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);


    $mailData['body'] = $mailBody;
    $mailData['recipient'] = $orderInfo->customer_email;
    $mailData['invoice'] = public_path('assets/file/invoices/service/') . $orderInfo->invoice;
    BasicMailer::sendMail($mailData);
    return;
  }

  public function cancel()
  {
    $notification = array(
      'message' => Session::get('error', 'Something went wrong'),
      'alert-type' => 'error'
    );

    return redirect()->route('frontend.services')->with($notification);
  }

  protected function saveBookingAnswers($bookingId, $questionAnswers)
  {
    try {
      $answers = is_string($questionAnswers) ? json_decode($questionAnswers, true) : $questionAnswers;

      if (is_array($answers)) {
        if (count($answers) === 0) {
          Log::info('No booking answers to save after decoding payload', [
            'booking_id' => $bookingId,
          ]);
          return;
        }

        $savedCount = 0;

        foreach ($answers as $questionId => $value) {
          $answer = is_array($value) ? json_encode(array_values($value)) : ($value === null ? null : (string) $value);

          BookingAnswer::create([
            'booking_id' => $bookingId,
            'question_id' => $questionId,
            'option_id' => null,
            'answer' => $answer
          ]);
          $savedCount++;
        }

        Log::info('Booking answers saved successfully', [
          'booking_id' => $bookingId,
          'saved_count' => $savedCount,
        ]);
      } else {
        Log::warning('Booking answers payload could not be decoded', [
          'booking_id' => $bookingId,
        ]);
      }
    } catch (\Exception $e) {
      \Log::error('Error saving booking answers:', ['error' => $e->getMessage()]);
    }
  }
}
