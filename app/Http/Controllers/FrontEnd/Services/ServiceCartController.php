<?php

namespace App\Http\Controllers\FrontEnd\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Models\BasicSettings\Basic;
use App\Models\Language;
use App\Models\PaymentGateway\OfflineGateway;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServicePackage;
use App\Models\Services\Services;
use App\Models\Shop\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ServiceCartController extends Controller
{
    public function quickAdd(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
        ]);

        $service = Services::findOrFail($request->service_id);
        $languageId = Language::query()->where('is_default', 1)->value('id');
        $serviceName = ServiceContent::query()
            ->where('service_id', $service->id)
            ->where('language_id', $languageId)
            ->value('name');

        $cart = Session::get('serviceCart', []);
        $itemKey = Str::uuid()->toString();

        $cart[$itemKey] = [
            'key' => $itemKey,
            'item_type' => 'service',
            'service_id' => $service->id,
            'service_name' => $serviceName ?? 'Service #' . $service->id,
            'price' => $this->resolveStartingPrice($service),
            'price_status' => $service->hasPricePlaceholder() ? 'pending' : 'fixed',
            'booking_date' => null,
            'service_hour_id' => null,
            'start_time' => null,
            'end_time' => null,
            'staff_id' => null,
            'max_person' => 1,
            'customer' => [],
            'site' => [],
            'billing_same' => 1,
            'billing' => [],
            'alternate' => [],
            'user_id' => Auth::guard('web')->check() ? Auth::guard('web')->id() : null,
            'vendor_id' => (int) ($service->vendor_id ?? 0),
            'zoom_status' => $service->zoom_meeting ?? 0,
            'calendar_status' => $service->calendar_status ?? 0,
            'question_answers' => null,
            'needs_checkout_details' => true,
        ];

        Session::put('serviceCart', $cart);

        return redirect()->route('frontend.services.cart')->with('success', __('Service added to cart'));
    }

    public function quickAddPackage(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer|exists:service_packages,id',
        ]);

        $package = ServicePackage::with(['services.content' => function ($query) {
            $query->where('language_id', Language::query()->where('is_default', 1)->value('id'));
        }])
            ->where('is_active', 1)
            ->findOrFail($request->package_id);

        $cart = Session::get('serviceCart', []);
        $itemKey = Str::uuid()->toString();
        $includedServices = $package->services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => optional($service->content->first())->name ?? 'Service #' . $service->id,
            ];
        })->values()->all();

        $cart[$itemKey] = [
            'key' => $itemKey,
            'item_type' => 'package',
            'package_id' => $package->id,
            'package_name' => $package->title,
            'service_id' => optional($package->services->first())->id,
            'service_name' => $package->title,
            'included_services' => $includedServices,
            'price' => $package->hasPricePlaceholder() ? 0.00 : $package->activePrice(),
            'price_status' => $package->hasPricePlaceholder() ? 'pending' : 'fixed',
            'booking_date' => null,
            'service_hour_id' => null,
            'start_time' => null,
            'end_time' => null,
            'staff_id' => null,
            'max_person' => 1,
            'customer' => [],
            'site' => [],
            'billing_same' => 1,
            'billing' => [],
            'alternate' => [],
            'user_id' => Auth::guard('web')->check() ? Auth::guard('web')->id() : null,
            'vendor_id' => 0,
            'zoom_status' => 0,
            'calendar_status' => 0,
            'question_answers' => null,
            'needs_checkout_details' => true,
        ];

        Session::put('serviceCart', $cart);

        return redirect()->route('frontend.services.cart')->with('success', __('Package added to cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'booking_date' => 'required|date',
            'service_hour_id' => 'nullable',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_person' => 'nullable|integer|min:1',
            'staff_id' => 'nullable',
            'question_answers' => 'nullable|string',
        ]);

        $service = Services::findOrFail($request->service_id);

        $languageId = Language::query()->where('is_default', 1)->value('id');
        $serviceName = ServiceContent::query()
            ->where('service_id', $service->id)
            ->where('language_id', $languageId)
            ->value('name');

        $cart = Session::get('serviceCart', []);
        $itemKey = Str::uuid()->toString();

        $cart[$itemKey] = [
            'key' => $itemKey,
            'item_type' => 'service',
            'service_id' => $service->id,
            'service_name' => $serviceName ?? 'Service #' . $service->id,
            'price' => round($request->price, 2),
            'price_status' => $service->hasPricePlaceholder() && round((float) $request->price, 2) <= 0
                ? 'pending'
                : ($service->hasPricePlaceholder() ? 'calculated' : 'fixed'),
            'booking_date' => $request->booking_date,
            'service_hour_id' => $request->service_hour_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'staff_id' => $request->staff_id ?? null,
            'max_person' => $request->max_person ?? 1,
            'customer' => [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
            ],
            'site' => $request->input('site', [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
            ]),
            'billing_same' => $request->input('billing_same', 1),
            'billing' => $request->input('billing', []),
            'alternate' => $request->input('alternate', []),
            'user_id' => $request->input('user_id'),
            'vendor_id' => (int) ($service->vendor_id ?? 0),
            'zoom_status' => $service->zoom_meeting ?? 0,
            'calendar_status' => $service->calendar_status ?? 0,
            'question_answers' => $request->input('question_answers'),
        ];

        Session::put('serviceCart', $cart);

        $cartTotal = collect($cart)->sum(function ($item) {
            return $item['price'];
        });

        return response()->json([
            'message' => __('Service added to cart'),
            'cart_count' => count($cart),
            'cart_total' => round($cartTotal, 2),
        ]);
    }

    public function index(Request $request)
    {
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        $data['pageHeading'] = $misc->getPageHeading($language);
        $data['bgImg'] = $misc->getBreadcrumb();
        $data['basicInfo'] = Basic::first();
        $data['currencyInfo'] = $this->getCurrencyInfo();
        $data['online_gateways'] = OnlineGateway::where('status', 1)->get();
        $data['offline_gateways'] = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();
        $data['authUser'] = Auth::guard('web')->user();
        $stripe = OnlineGateway::where('keyword', 'stripe')->first();
        $stripeInfo = $stripe ? json_decode($stripe->information, true) : [];
        $data['stripe_key'] = $stripeInfo['key'] ?? '';

        $authorizeNet = OnlineGateway::where('keyword', 'authorize.net')->first();
        $authorizeInfo = $authorizeNet ? json_decode($authorizeNet->information, true) : [];
        $data['authorizeUrl'] = (($authorizeInfo['sandbox_check'] ?? 0) == 1)
            ? 'https://jstest.authorize.net/v1/Accept.js'
            : 'https://js.authorize.net/v1/Accept.js';
        $data['authorize_login_id'] = $authorizeInfo['login_id'] ?? '';
        $data['authorize_public_key'] = $authorizeInfo['public_key'] ?? '';

        $cart = Session::get('serviceCart', []);
        $serviceIds = collect($cart)->filter(function ($item) {
                return ($item['item_type'] ?? 'service') === 'service';
            })
            ->pluck('service_id')
            ->filter()
            ->unique()
            ->values();
        $services = Services::query()
            ->whereIn('id', $serviceIds)
            ->get()
            ->keyBy('id');
        $packageIds = collect($cart)->filter(function ($item) {
                return ($item['item_type'] ?? 'service') === 'package';
            })
            ->pluck('package_id')
            ->filter()
            ->unique()
            ->values();
        $packages = ServicePackage::with(['services.content' => function ($query) use ($language) {
                $query->where('language_id', $language->id);
            }])
            ->whereIn('id', $packageIds)
            ->get()
            ->keyBy('id');

        foreach ($cart as $key => $item) {
            if (($item['item_type'] ?? 'service') === 'package') {
                $package = $packages->get($item['package_id'] ?? null);

                if (!$package || !$package->is_active) {
                    unset($cart[$key]);
                    continue;
                }

                $cart[$key]['package_name'] = $package->title;
                $cart[$key]['service_name'] = $package->title;
                if ($package->hasPricePlaceholder() && ($item['price_status'] ?? null) !== 'calculated') {
                    $cart[$key]['price'] = 0.00;
                    $cart[$key]['price_status'] = 'pending';
                } else {
                    $cart[$key]['price'] = $package->activePrice();
                    $cart[$key]['price_status'] = 'fixed';
                }
                $cart[$key]['included_services'] = $package->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => optional($service->content->first())->name ?? 'Service #' . $service->id,
                    ];
                })->values()->all();
                continue;
            }

            $service = $services->get($item['service_id'] ?? null);

            if ($service && $service->hasPricePlaceholder() && ($item['price_status'] ?? null) !== 'calculated') {
                $cart[$key]['price'] = 0.00;
                $cart[$key]['price_status'] = 'pending';
                continue;
            }

            if ((float) ($item['price'] ?? 0) > 0) {
                continue;
            }

            if ($service) {
                $cart[$key]['price'] = $this->resolveStartingPrice($service);
                $cart[$key]['price_status'] = 'fixed';
            }
        }

        Session::put('serviceCart', $cart);

        $serviceCart = collect($cart);
        $data['serviceCart'] = $serviceCart;
        $data['cartCount'] = $serviceCart->count();
        $data['cartTotal'] = $serviceCart->sum(function ($item) {
            return $item['price'];
        });
        $data['serviceCouponCode'] = Session::get('serviceCouponCode');
        $data['serviceCouponDiscount'] = min((float) Session::get('serviceCouponDiscount', 0), (float) $data['cartTotal']);
        $data['serviceCouponType'] = Session::get('serviceCouponType');
        $data['serviceCouponValue'] = (float) Session::get('serviceCouponValue', 0);

        $data['serviceQuestions'] = Question::query()
            ->whereIn('service_id', $serviceIds)
            ->with('options', 'conditions.dependsOnQuestion', 'conditions.dependsOnOption')
            ->get()
            ->groupBy('service_id');

        $data['packageQuestions'] = Question::query()
            ->whereIn('service_package_id', $packageIds)
            ->with('options', 'conditions.dependsOnQuestion', 'conditions.dependsOnOption')
            ->get()
            ->groupBy('service_package_id');

        return view('frontend.services.cart', $data);
    }

    public function remove(Request $request, string $key)
    {
        $cart = Session::get('serviceCart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::put('serviceCart', $cart);
        }

        if (empty($cart)) {
            Session::forget('serviceCart');
            $this->forgetCoupon();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Service removed from cart'),
                'cart_count' => count(Session::get('serviceCart', [])),
            ]);
        }

        return redirect()->route('frontend.services.cart')->with('success', __('Service removed from cart'));
    }

    public function clear()
    {
        Session::forget('serviceCart');
        $this->forgetCoupon();

        return redirect()->route('frontend.services.cart')->with('success', __('Cart cleared'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'required|string|max:255',
            'total' => 'nullable|numeric|min:0',
        ]);

        try {
            $coupon = Coupon::where('code', $request->coupon)->firstOrFail();
            $todayDate = Carbon::now();

            if (!$todayDate->between(Carbon::parse($coupon->start_date), Carbon::parse($coupon->end_date))) {
                return response()->json(['error' => __('Sorry, coupon has expired!')], 422);
            }

            $cartItems = collect(Session::get('serviceCart', []));

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => __('Your service cart is empty.')], 422);
            }

            $total = round((float) $request->input('total', $cartItems->sum('price')), 2);

            if ($total <= 0) {
                return response()->json(['error' => __('The booking total must be greater than zero before applying a coupon.')], 422);
            }

            if (!empty($coupon->minimum_spend) && $total < (float) $coupon->minimum_spend) {
                $currencyInfo = $this->getCurrencyInfo();
                $position = $currencyInfo->base_currency_symbol_position;
                $symbol = $currencyInfo->base_currency_symbol;
                $spendData = ($position == 'left' ? $symbol : '') . number_format((float) $coupon->minimum_spend, 2) . ($position == 'right' ? $symbol : '');

                return response()->json([
                    'error' => __('You have to book at least') . ' ' . $spendData . ' ' . __('of services.'),
                ], 422);
            }

            $discount = $coupon->type == 'fixed'
                ? (float) $coupon->value
                : $total * ((float) $coupon->value / 100);
            $discount = round(min(max($discount, 0), $total), 2);

            Session::put('serviceCouponCode', $coupon->code);
            Session::put('serviceCouponDiscount', $discount);
            Session::put('serviceCouponType', $coupon->type);
            Session::put('serviceCouponValue', (float) $coupon->value);

            return response()->json([
                'success' => __('Coupon applied successfully.'),
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'amount' => $discount,
                'total' => round($total - $discount, 2),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => __('Coupon is not valid!')], 422);
        }
    }

    public function removeCoupon()
    {
        $this->forgetCoupon();

        return response()->json([
            'success' => __('Coupon removed successfully.'),
        ]);
    }

    public function summary()
    {
        $serviceCart = collect(Session::get('serviceCart', []));
        $currency = $this->getCurrencyInfo();
        $total = $serviceCart->sum('price');

        return view('frontend.services.booking-modal.partials.cart-summary', [
            'serviceCart' => $serviceCart,
            'cartCount' => $serviceCart->count(),
            'currencySymbol' => $currency->base_currency_symbol,
            'currencyPosition' => $currency->base_currency_symbol_position,
            'cartTotal' => $total,
        ]);
    }

    private function resolveStartingPrice(Services $service): float
    {
        if ($service->hasPricePlaceholder()) {
            return 0.00;
        }

        $price = (float) ($service->price ?? 0);

        if ($price > 0) {
            return round($price, 2);
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

        return round((float) ($quantityPrice ?? 0), 2);
    }

    private function forgetCoupon(): void
    {
        Session::forget('serviceCouponCode');
        Session::forget('serviceCouponDiscount');
        Session::forget('serviceCouponType');
        Session::forget('serviceCouponValue');
    }
}
