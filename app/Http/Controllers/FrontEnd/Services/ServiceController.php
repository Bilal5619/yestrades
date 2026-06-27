<?php

namespace App\Http\Controllers\FrontEnd\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\CheckLimitHelper;
use App\Models\Admin;
use App\Models\Admin\AdminGlobalDay;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\PaymentGateway\OfflineGateway;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Services\InqueryMessage;
use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServicePackage;
use App\Models\Services\ServiceReview;
use App\Models\Services\Services;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffDay;
use App\Models\Staff\StaffGlobalDay;
use App\Models\Staff\StaffGlobalHoliday;
use App\Models\Staff\StaffGlobalHour;
use App\Models\Staff\StaffHoliday;
use App\Models\Staff\StaffService;
use App\Models\Staff\StaffServiceHour;
use App\Models\Vendor;
use App\Models\User;
use App\Models\PostalCode;
use App\Models\Question;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Response;
use Session;

class ServiceController extends Controller
{
  /**
   * services page
   */
  public function index(Request $request)
  {
    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();
    $information['pageHeading'] = $misc->getPageHeading($language);
    //active or deactive vendor check
    $vendorStatus = Vendor::where('status', 1)->select('id')->get()->toArray();


    //service count category wise
    $categories = ServiceCategory::where('language_id', $language->id)->get();
    foreach ($categories as $category) {
      $serviceCount = Services::join(
        'service_contents',
        'service_contents.service_id',
        '=',
        'services.id',
      )
        ->where(function ($query) use ($vendorStatus) {
          $query->whereIn('services.vendor_id', $vendorStatus)
            ->orWhere('services.vendor_id', 0);
        })
        ->when('services.vendor_id' != '0', function ($query) {
          return $query
            ->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
            ->where(function ($query) {
              $query
                ->where([
                  ['memberships.status', '=', 1],
                  ['memberships.start_date', '<=', now()->format('Y-m-d')],
                  ['memberships.expire_date', '>=', now()->format('Y-m-d')],
                ])
                ->orWhere('services.vendor_id', '=', 0);
            });
        })
        ->where('service_contents.category_id', $category->id)
        ->where('services.status', 1)
        ->where('service_contents.language_id', $category->language_id)
        ->count();
      $category->service_count = $serviceCount;
    }
    $information['categories'] = $categories;

    $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_services', 'meta_description_services')->first();
    $information['bgImg'] = $misc->getBreadcrumb();

    $location  = $serviceName = $category = null;

    //search by category
    $category_serviceId = [];
    if ($request->filled('category')) {
      $category = $request->category;
      $category_content = ServiceCategory::where([['language_id', $language->id], ['slug', $category]])->first();

      if (!empty($category_content)) {
        $category_id = $category_content->id;
        $contents = ServiceContent::where('language_id', $language->id)
          ->where('category_id', $category_id)
          ->get()
          ->pluck('service_id');
        foreach ($contents as $content) {
          if (!in_array($content, $category_serviceId)) {
            array_push($category_serviceId, $content);
          }
        }
      }
    }

    $locationIds = [];

    if ($request->filled('location')) {
      $location = preg_replace('/\s+/', '', $request->location); // Remove spaces
      $inputLength = strlen($location);

      $prefixes = [];

      // Build prefix list from most specific to least
      if ($inputLength >= 3) {
        $prefixes[] = substr($location, 0, 3);
        $prefixes[] = substr($location, 0, 2);
        $prefixes[] = substr($location, 0, 1);
      } elseif ($inputLength == 2) {
        $prefixes[] = substr($location, 0, 2);
        $prefixes[] = substr($location, 0, 1);
      } elseif ($inputLength == 1) {
        $prefixes[] = substr($location, 0, 1);
      }

      foreach ($prefixes as $prefix) {
        // Escape special regex characters
        $safePrefix = preg_quote($prefix, '/');

        $locationIds = ServiceContent::where('language_id', $language->id)
          ->where(function ($query) use ($safePrefix) {
            $query->whereRaw(
              "postcode REGEXP ?",
              ["(^|,)" . $safePrefix . "[0-9]*($|,)"]
            )
              ->orWhereNull('postcode')
              ->orWhere('postcode', '');
          })
          ->pluck('service_id')
          ->unique()
          ->toArray();

        if (!empty($locationIds)) {
          break; // Stop if matches found
        }
      }
    }



    //search by service name
    $serviceNameIds = [];
    if ($request->filled('service_title')) {
      $serviceName = $request->service_title;

      $contents = ServiceContent::where('language_id', $language->id)
        ->where('name', 'like', '%' . $serviceName . '%')
        ->get()
        ->pluck('service_id');
      foreach ($contents as $content) {
        if (!in_array($content, $serviceNameIds)) {
          array_push($serviceNameIds, $content);
        }
      }
    }

    $featuredServices = Services::join('service_promotions', 'service_promotions.service_id', '=', 'services.id')
      ->join('service_contents', 'services.id', '=', 'service_contents.service_id')
      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
      ->where('service_contents.language_id', $language->id)
      ->where('services.status', 1)
      ->where(function ($query) use ($vendorStatus) {
        $query->whereIn('services.vendor_id', $vendorStatus)
          ->orWhere('services.vendor_id', 0);
      })
      ->where('service_promotions.order_status', '=', 'approved')
      ->where('service_promotions.payment_status', '=', 'completed')
      ->whereDate('service_promotions.end_date', '>=', Carbon::now()->format('Y-m-d'))
      ->when('services.vendor_id' != "0", function ($query) {
        return $query->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
          ->where(function ($query) {
            $query->where([
              ['memberships.status', '=', 1],
              ['memberships.start_date', '<=', now()->format('Y-m-d')],
              ['memberships.expire_date', '>=', now()->format('Y-m-d')],
            ])->orWhere('services.vendor_id', '=', 0);
          });
      })
      ->when($category, function ($query) use ($category_serviceId) {
        return $query->whereIn('services.id', $category_serviceId);
      })
      ->when($location, function ($query) use ($locationIds) {
        return $query->whereIn('services.id', $locationIds);
      })
      ->when($serviceName, function ($query) use ($serviceNameIds) {
        return $query->whereIn('services.id', $serviceNameIds);
      })
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_contents.address',
        'service_categories.name as categoryName',
        'service_categories.id as categoryid',
      )
      ->inRandomOrder()
      ->take(3)
      ->get();

    $information['featuredServices'] = $featuredServices;

    $renewalServices = collect();
    $renewalPackages = collect();

    if (Schema::hasColumn('services', 'show_in_renewal_section')) {
      $renewalServices = Services::join('service_contents', 'services.id', '=', 'service_contents.service_id')
      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
      ->where('service_contents.language_id', $language->id)
      ->where('services.status', 1)
      ->where('services.show_in_renewal_section', 1)
      ->where(function ($query) use ($vendorStatus) {
        $query->whereIn('services.vendor_id', $vendorStatus)
          ->orWhere('services.vendor_id', 0);
      })
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_categories.name as categoryName'
      )
      ->orderBy('services.renewal_section_sort_order')
      ->orderByDesc('services.id')
      ->get()
      ->map(function ($service) {
        $service->renewal_item_type = 'service';
        $service->renewal_title = $service->name;
        $service->renewal_description = $service->renewal_short_description;
        $service->renewal_price = $service->price;
        $service->renewal_price_placeholder = $service->price_placeholder;
        $service->renewal_offer_price = $service->offer_price;
        $service->renewal_offer_label = $service->offer_label;
        $service->renewal_image = $service->service_image;
        $service->renewal_sort_order = $service->renewal_section_sort_order;
        return $service;
      });
    }

    if (Schema::hasTable('service_packages')) {
      $renewalPackages = ServicePackage::with(['services.content' => function ($query) use ($language) {
        $query->where('language_id', $language->id);
      }])
      ->where('is_active', 1)
      ->where('show_in_renewal_section', 1)
      ->orderBy('renewal_section_sort_order')
      ->orderByDesc('id')
      ->get()
      ->map(function ($package) {
        $package->renewal_item_type = 'package';
        $package->renewal_title = $package->title;
        $package->renewal_description = $package->short_description;
        $package->renewal_price = $package->price;
        $package->renewal_price_placeholder = $package->price_placeholder;
        $package->renewal_offer_price = $package->offer_price;
        $package->renewal_offer_label = $package->offer_label;
        $package->renewal_image = $package->image;
        $package->renewal_sort_order = $package->renewal_section_sort_order;
        return $package;
      });
    }

    $information['renewalItems'] = $renewalServices
      ->concat($renewalPackages)
      ->sortBy([
        ['renewal_sort_order', 'asc'],
        ['id', 'desc'],
      ])
      ->values();

    $numFeaturedServices = $featuredServices->count();
    $numRegularServices = max(0, 12 - $numFeaturedServices);

    $featuredServiceIds = $featuredServices->pluck('id')->toArray();

    $services = Services::with('vendor')
      ->join('service_contents', 'services.id', '=', 'service_contents.service_id')
      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
      ->where('service_contents.language_id', $language->id)
      ->where('services.status', 1)
      ->whereNotIn('services.id', $featuredServiceIds)
      ->where(function ($query) use ($vendorStatus) {
        $query->whereIn('services.vendor_id', $vendorStatus)
          ->orWhere('services.vendor_id', 0);
      })
      ->when('services.vendor_id' != "0", function ($query) {
        return $query->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
          ->where(function ($query) {
            $query->where([
              ['memberships.status', '=', 1],
              ['memberships.start_date', '<=', now()->format('Y-m-d')],
              ['memberships.expire_date', '>=', now()->format('Y-m-d')],
            ])->orWhere('services.vendor_id', '=', 0);
          });
      })
      ->when($category, function ($query) use ($category_serviceId) {
        return $query->whereIn('services.id', $category_serviceId);
      })
      ->when($location, function ($query) use ($locationIds) {
        return $query->whereIn('services.id', $locationIds);
      })
      ->when($serviceName, function ($query) use ($serviceNameIds) {
        return $query->whereIn('services.id', $serviceNameIds);
      })
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_contents.address',
        'service_categories.name as categoryName',
        'service_categories.id as categoryid',
      )
      ->orderBy('services.created_at', 'desc')
      ->paginate($numRegularServices);

    //total service count
    $information['services'] = $services;
    $featuredServiceCount = $featuredServices->count();
    $serviceCount = $services->total();
    $information['total_services'] = $featuredServiceCount + $serviceCount;

    //admin info
    $information['admin'] = Admin::whereNull('role_id')->firstOrFail();

    //sidebara search
    $information['min'] = Services::where('status', 1)->min('price');
    $information['max'] = Services::where('status', 1)->max('price');

    //Strip payment
    $stripe = OnlineGateway::where('keyword', 'stripe')->first();
    $stripe_info = json_decode($stripe->information, true);
    $information['stripe_key'] = $stripe_info['key'];

    //Authorize.Net payment
    $authorizeNet = OnlineGateway::where('keyword', 'authorize.net')->first();
    $authorizeInfo = json_decode($authorizeNet->information, true);

    if ($authorizeInfo['sandbox_check'] == 1) {
      $information['authorizeUrl'] = 'https://jstest.authorize.net/v1/Accept.js';
    } else {
      $information['authorizeUrl'] = 'https://js.authorize.net/v1/Accept.js';
    }

    $information['authorize_login_id'] = $authorizeInfo['login_id'];
    $information['authorize_public_key'] = $authorizeInfo['public_key'];

    if (Session::has('complete') || Session::has('paymentInfo')) {
      Log::info('Rendering services listing with payment session state', [
        'host' => $request->getHost(),
        'session_id' => $request->session()->getId(),
        'complete' => Session::get('complete'),
        'payment_info_id' => optional(Session::get('paymentInfo'))->id,
      ]);
    }

    return view('frontend.services.service_list', $information);
  }

  /**
   * service search pagination & sort
   */
  public function searchService(Request $request)
  {
    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();
    //admin info
    $information['admin'] = Admin::whereNull('role_id')->firstOrFail();
    $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_services', 'meta_description_services')->first();
    $information['bgImg'] = $misc->getBreadcrumb();


    $min = $max = $category = $rating = $sort = $location = $service_type = $serviceName = null;

    //search by category
    $category_serviceId = [];
    if ($request->filled('category')) {
      $category = $request->category;
      $category_content = ServiceCategory::where([['language_id', $language->id], ['slug', $category]])->first();
      if (!empty($category_content)) {
        $category_id = $category_content->id;
        $contents = ServiceContent::where('language_id', $language->id)
          ->where('category_id', $category_id)
          ->get()
          ->pluck('service_id');
        foreach ($contents as $content) {
          if (!in_array($content, $category_serviceId)) {
            array_push($category_serviceId, $content);
          }
        }
      }
    }
    //search by price
    $priceIds = [];
    if ($request->filled('min_val') && $request->filled('max_val')) {
      $min = intval($request->min_val);
      $max = intval(($request->max_val));
      $price_servicess = DB::table('services')
        ->select('*')
        ->where('price', '>=', $min)
        ->where('price', '<=', DB::raw($max))
        ->get();
      foreach ($price_servicess as $service) {
        if (!in_array($service->id, $priceIds)) {
          array_push($priceIds, $service->id);
        }
      }
    }
    //search by location
    $locationIds = [];
    if ($request->filled('location_val')) {
      $location = $request->location_val;
      $contents = ServiceContent::where('language_id', $language->id)
        ->where('address', 'like', '%' . $location . '%')
        ->get()
        ->pluck('service_id');
      foreach ($contents as $content) {
        if (!in_array($content, $locationIds)) {
          array_push($locationIds, $content);
        }
      }
    }

    //search by service name
    $serviceNameIds = [];
    if ($request->filled('service_title')) {
      $serviceName = $request->service_title;

      $contents = ServiceContent::where('language_id', $language->id)
        ->where('name', 'like', '%' . $serviceName . '%')
        ->get()
        ->pluck('service_id');
      foreach ($contents as $content) {
        if (!in_array($content, $serviceNameIds)) {
          array_push($serviceNameIds, $content);
        }
      }
    }

    //search by service type
    $serviceTypeId = [];
    if ($request->filled('service_type')) {
      $service_type = $request->service_type;

      if ($service_type == 'online') {
        $services = Services::where('zoom_meeting', 1)->get()->pluck('id');
      } elseif ($service_type == 'offline') {
        $services = Services::where('zoom_meeting', 0)->get()->pluck('id');
      } else {
        $services = Services::query()->get()->pluck('id');
      }
      foreach ($services as $service) {
        if (!in_array($service, $serviceTypeId)) {
          array_push($serviceTypeId, $service);
        }
      }
    }


    //search by rating
    if ($request->filled('rating')) {
      $rating = floatval($request['rating']);
    }
    if ($request->filled('sort_val')) {
      $sort = $request['sort_val'];
    }

    $vendorStatus = Vendor::where('status', 1)->select('id')->get()->toArray();

    $featuredServices = Services::join('service_promotions', 'service_promotions.service_id', '=', 'services.id')
      ->join('service_contents', 'services.id', '=', 'service_contents.service_id')
      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
      ->where('service_contents.language_id', $language->id)
      ->where('services.status', 1)
      ->where(function ($query) use ($vendorStatus) {
        $query->whereIn('services.vendor_id', $vendorStatus)
          ->orWhere('services.vendor_id', 0);
      })
      ->where('service_promotions.order_status', '=', 'approved')
      ->whereDate('service_promotions.end_date', '>=', Carbon::now()->format('Y-m-d'))
      ->when('services.vendor_id' != "0", function ($query) {
        return $query->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
          ->where(function ($query) {
            $query->where([
              ['memberships.status', '=', 1],
              ['memberships.start_date', '<=', now()->format('Y-m-d')],
              ['memberships.expire_date', '>=', now()->format('Y-m-d')],
            ])->orWhere('services.vendor_id', '=', 0);
          });
      })
      ->when($category, function ($query) use ($category_serviceId) {
        return $query->whereIn('services.id', $category_serviceId);
      })
      ->when(($min && $max), function ($query) use ($priceIds) {
        return $query->whereIn('services.id', $priceIds);
      })
      ->when($rating, function ($query, $rating) {
        return $query->where('services.average_rating', '>=', $rating);
      })
      ->when($location, function ($query) use ($locationIds) {
        return $query->whereIn('services.id', $locationIds);
      })
      ->when($serviceName, function ($query) use ($serviceNameIds) {
        return $query->whereIn('services.id', $serviceNameIds);
      })
      ->when($service_type, function ($query) use ($serviceTypeId) {
        return $query->whereIn('services.id', $serviceTypeId);
      })
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_contents.address',
        'service_categories.name as categoryName',
        'service_categories.id as categoryid',
      )
      ->inRandomOrder()
      ->limit(3)
      ->get();

    $information['featuredServices'] = $featuredServices;

    $numFeaturedServices = $featuredServices->count();
    $numRegularServices = max(0, 12 - $numFeaturedServices);

    $featuredServiceIds = $featuredServices->pluck('id')->toArray();

    $services = Services::join('service_contents', 'services.id', '=', 'service_contents.service_id')
      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
      ->where('service_contents.language_id', $language->id)
      ->where('services.status', 1)
      ->where(function ($query) use ($vendorStatus) {
        $query->whereIn('services.vendor_id', $vendorStatus)
          ->orWhere('services.vendor_id', 0);
      })
      ->whereNotIn('services.id', $featuredServiceIds)
      ->when('services.vendor_id' != "0", function ($query) {
        return $query->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
          ->where(function ($query) {
            $query->where([
              ['memberships.status', '=', 1],
              ['memberships.start_date', '<=', now()->format('Y-m-d')],
              ['memberships.expire_date', '>=', now()->format('Y-m-d')],
            ])->orWhere('services.vendor_id', '=', 0);
          });
      })
      ->when($category, function ($query) use ($category_serviceId) {
        return $query->whereIn('services.id', $category_serviceId);
      })
      ->when(($min && $max), function ($query) use ($priceIds) {
        return $query->whereIn('services.id', $priceIds);
      })
      ->when($rating, function ($query, $rating) {
        return $query->where('services.average_rating', '>=', $rating);
      })
      ->when($location, function ($query) use ($locationIds) {
        return $query->whereIn('services.id', $locationIds);
      })
      ->when($serviceName, function ($query) use ($serviceNameIds) {
        return $query->whereIn('services.id', $serviceNameIds);
      })
      ->when($service_type, function ($query) use ($serviceTypeId) {
        return $query->whereIn('services.id', $serviceTypeId);
      })
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_contents.address',
        'service_categories.name as categoryName',
        'service_categories.id as categoryid',
      )->when($sort, function ($query, $sort) {
        if ($sort == 'newest') {
          return $query->orderBy('services.created_at', 'desc');
        } else if ($sort == 'old') {
          return $query->orderBy('services.created_at', 'asc');
        } else if ($sort == 'high-to-low') {
          return $query->orderBy('services.price', 'desc');
        } else if ($sort == 'low-to-high') {
          return $query->orderBy('services.price', 'asc');
        }
      }, function ($query) {
        return $query->orderByDesc('services.created_at');
      })
      ->paginate($numRegularServices);
    $information['services'] = $services;


    $information['min'] = Services::where('status', 1)->min('price');
    $information['max'] = Services::where('status', 1)->max('price');

    //Strip payment
    $stripe = OnlineGateway::where('keyword', 'stripe')->first();
    $stripe_info = json_decode($stripe->information, true);
    $information['stripe_key'] = $stripe_info['key'];

    //Authorize.Net payment
    $authorizeNet = OnlineGateway::where('keyword', 'authorize.net')->first();
    $authorizeInfo = json_decode($authorizeNet->information, true);

    if ($authorizeInfo['sandbox_check'] == 1) {
      $information['authorizeUrl'] = 'https://jstest.authorize.net/v1/Accept.js';
    } else {
      $information['authorizeUrl'] = 'https://js.authorize.net/v1/Accept.js';
    }

    $information['authorize_login_id'] = $authorizeInfo['login_id'];
    $information['authorize_public_key'] = $authorizeInfo['public_key'];


    return view('frontend.services.search.search-services', $information)->render();
  }

  /**
   * show staff content
   */
  public function staffcontent($id)
  {
    try {
      // Get service info
      $service = Services::find($id);

      if (!$service) {
        return response()->json(['error' => 'Service not found.'], 404);
      }

      // Create minimal required data
      $information = [];

      // Create a default staff entry for general booking
      $defaultStaff = (object) [
        'id' => '11done',
        'name' => '11DONE Service',
        'is_day' => 0,
        'role' => 'vendor',
        'vendor_id' => $service->vendor_id,
        'email' => '',
        'image' => 'default.png',
        'email_status' => 0,
        'status' => 1,
        'username' => '11done'
      ];

      $information['staffs'] = collect([$defaultStaff]);
      $information['staffCount'] = 1;
      $information['staffService'] = 1;

      // Store service data in session
      Session::put('serviceData', [
        'vendor_id' => (int) ($service->vendor_id ?? 0),
        'service_ammount' => $service->price,
        'service_id' => $service->id,
        'zoom_status' => $service->zoom_meeting ?? 0,
        'calendar_status' => $service->calendar_status ?? 0,
      ]);

      // Get basic settings
      $information['bs'] = Basic::settings(['google_recaptcha_status']);
      $information['authUser'] = Auth::guard('web')->check() ? Auth::guard('web')->user() : null;
      $information['groupService'] = Services::where('id', $id)->select('max_person')->first();

      // Resolve default language service name
      $defaultLanguage = Language::query()->where('is_default', 1)->first();
      $serviceName = null;
      if ($defaultLanguage) {
        $serviceName = ServiceContent::query()
          ->where('service_id', $service->id)
          ->where('language_id', $defaultLanguage->id)
          ->value('name');
      }

      // Get payment gateways
      $information['online_gateways'] = OnlineGateway::where('status', 1)->get();
      $information['offline_gateways'] = OfflineGateway::where('status', 1)->orderBy('serial_number', 'desc')->get();

      // Pass service and vendor IDs to the view for payment form
      $information['service_id'] = $service->id;
      $information['vendor_id'] = (int) ($service->vendor_id ?? 0);
      $information['service_price'] = $service->price;
      $information['service_name'] = $serviceName ?? $service->id;
      $cartItems = Session::get('serviceCart', []);
      $information['service_cart_items'] = $cartItems;
      $information['service_cart_total'] = collect($cartItems)->sum('price');
      $information['currencyInfo'] = $this->getCurrencyInfo();

      // Fetch questions for this service
      $questions = \App\Models\Question::where('service_id', $service->id)
        ->with('options', 'conditions.dependsOnQuestion', 'conditions.dependsOnOption')
        ->get();
      $information['questions'] = $questions;

      return view('frontend.services.booking-modal.service-modal', $information);
    } catch (\Exception $e) {
      \Log::error('Staff content error: ' . $e->getMessage());
      return response()->json(['error' => 'Service temporarily unavailable. Please try again.'], 500);
    }
  }

  /**
   * service details page
   */
  public function details($slug, $id)
  {
    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();

    $currentLang =  Language::where('is_default', 1)->first();

    $information['admin'] = Admin::whereNull('role_id')->firstOrFail();

    $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_services', 'meta_description_services')->first();
    $information['bgImg'] = $misc->getBreadcrumb();
    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    //service details
    $serviceDetails = Services::with(['content' => function ($query) use ($language) {
      return $query->where('language_id', $language->id);
    }, 'sliderImage'])
      ->with(['vendorInfo' => function ($query) use ($language) {
        return $query->where('language_id', $language->id);
      }])
      ->when('services.vendor_id' != "0", function ($query) {
        return $query->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
          ->where(function ($query) {
            $query->where([
              ['memberships.status', '=', 1],
              ['memberships.start_date', '<=', now()->format('Y-m-d')],
              ['memberships.expire_date', '>=', now()->format('Y-m-d')],
            ])->orWhere('services.vendor_id', '=', 0);
          });
      })
      ->where('services.status', 1)
      ->select('services.*')
      ->where('services.id', $id)->firstOrFail();
    if ($serviceDetails->content->isEmpty()) {
      session()->put('currentLocaleCode', $currentLang->code);
      return redirect()->back()->with('warning', 'Content not available. Please try another language.');
    }

    $information['details'] = $serviceDetails;

    $information['reviews'] = ServiceReview::approved()->where('service_id', $id)->get();


    //category related service
    $service_content = ServiceContent::where('language_id', $language->id)->where('service_id', $id)->first();
    if ($service_content) {
      $category_id = $service_content->category_id;
      $information['related_services'] = Services::join('service_contents', 'services.id', '=', 'service_contents.service_id')
        ->join('service_categories', 'service_contents.category_id', '=', 'service_categories.id')
        ->where('service_contents.language_id', $language->id)
        ->where('service_categories.language_id', $language->id)
        ->where('services.id', '!=', $id)
        ->where('services.status', 1)
        ->where('service_contents.category_id', $category_id)
        ->when('services.vendor_id' != "0", function ($query) {
          return $query->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
            ->where(function ($query) {
              $query->where([
                ['memberships.status', '=', 1],
                ['memberships.start_date', '<=', now()->format('Y-m-d')],
                ['memberships.expire_date', '>=', now()->format('Y-m-d')],
              ])->orWhere('services.vendor_id', '=', 0);
            });
        })
        ->select('services.*', 'service_contents.slug', 'service_contents.name', 'service_contents.address', 'service_categories.name as category_name', 'service_categories.id as category_id')
        ->orderBy('services.created_at', 'desc')
        ->get();
    } else {
      $information['related_services'] = collect();
    }

    $globalDaysTable = ($serviceDetails->vendor_id != 0) ? 'staff_global_days' : 'admin_global_days';

    $serviceDays = StaffGlobalHour::join($globalDaysTable, 'staff_global_hours.global_day_id', '=', $globalDaysTable . '.id')
      ->where('staff_global_hours.vendor_id', $serviceDetails->vendor_id)
      ->selectRaw('min(start_time) AS minTime, max(end_time) AS maxTime, ' . $globalDaysTable . '.indx, ' . $globalDaysTable . '.is_weekend, ' . $globalDaysTable . '.day, ' . $globalDaysTable . '.id')
      ->groupBy('staff_global_hours.global_day_id', $globalDaysTable . '.indx', $globalDaysTable . '.is_weekend', $globalDaysTable . '.day', $globalDaysTable . '.id')
      ->get();

    $reordered = $serviceDays->map(function ($item, $key) {
      return [
        'dayId' => $item['id'],
        'minTime' => $item['minTime'],
        'maxTime' => $item['maxTime'],
        'day' => $item['day'],
        'is_weekend' => $item['is_weekend'],
        'indx' => $item['indx'],
      ];
    })->sortBy('indx');
    $information['allDays'] = $reordered;


    //Strip payment
    $stripe = OnlineGateway::where('keyword', 'stripe')->first();
    $stripe_info = json_decode($stripe->information, true);
    $information['stripe_key'] = $stripe_info['key'];

    //Authorize.Net payment
    $authorizeNet = OnlineGateway::where('keyword', 'authorize.net')->first();
    $authorizeInfo = json_decode($authorizeNet->information, true);

    if ($authorizeInfo['sandbox_check'] == 1) {
      $information['authorizeUrl'] = 'https://jstest.authorize.net/v1/Accept.js';
    } else {
      $information['authorizeUrl'] = 'https://js.authorize.net/v1/Accept.js';
    }

    $information['authorize_login_id'] = $authorizeInfo['login_id'];
    $information['authorize_public_key'] = $authorizeInfo['public_key'];

    if (Session::has('complete') || Session::has('paymentInfo')) {
      Log::info('Rendering service details with payment session state', [
        'session_id' => session()->getId(),
        'service_id' => $id,
        'complete' => Session::get('complete'),
        'payment_info_id' => optional(Session::get('paymentInfo'))->id,
      ]);
    }

    return view('frontend.services.details', $information);
  }

  /**
   * show staff service hour
   */
  public function staffHour(Request $request)
  {
    $staff = Staff::find($request->staff_id);
    $information['staff'] = $staff;

    //count same hour from booking table
    $bookedIds = [];

    $service_bookings = ServiceBooking::where('staff_id', $request->staff_id)
      ->where('booking_date', $request->bookingDate)
      ->select('service_hour_id')->get();

    foreach ($service_bookings as $service_booking) {
      array_push($bookedIds, $service_booking->service_hour_id);
    }

    $bookedCount = count($bookedIds);
    $information['staff_time'] = StaffServiceHour::leftJoin('staff_days', 'staff_days.id', '=', 'staff_service_hours.staff_day_id')
      ->leftJoin('service_bookings', function ($join) use ($request, $bookedCount) {
        $join->on('service_bookings.service_hour_id', '=', 'staff_service_hours.id')
          ->where('service_bookings.booking_date', '=', $request->bookingDate)
          ->where(function ($query) use ($bookedCount) {
            $query->where('staff_service_hours.max_booking', '<=', $bookedCount);
          })
          ->where('service_bookings.order_status', '!=', 'rejected')
          ->where('service_bookings.staff_id', '=', $request->staff_id);
      })
      ->whereNull('service_bookings.id')
      ->where('staff_days.staff_id', $request->staff_id)
      ->where('staff_service_hours.staff_id', $request->staff_id)
      ->where('staff_days.day', $request->dayName)
      ->select('staff_days.day', 'staff_service_hours.start_time', 'staff_service_hours.end_time', 'staff_service_hours.id', 'staff_service_hours.staff_id', 'staff_service_hours.max_booking')
      ->get();

    if ($request->vendor_id != 0) {
      $information['global_time'] = StaffGlobalHour::join('staff_global_days', 'staff_global_days.id', '=', 'staff_global_hours.global_day_id')
        ->leftJoin('service_bookings', function ($join) use ($request, $bookedCount) {
          $join->on('service_bookings.service_hour_id', '=', 'staff_global_hours.id')
            ->where('service_bookings.booking_date', '=', $request->bookingDate)
            ->where(function ($query) use ($bookedCount) {
              $query->where('staff_global_hours.max_booking', '<=', $bookedCount);
            })
            ->where('service_bookings.order_status', '!=', 'rejected')
            ->where('service_bookings.staff_id', '=', $request->staff_id);
        })
        ->whereNull('service_bookings.id')
        ->where('staff_global_hours.vendor_id', $request->vendor_id)
        ->where('staff_global_days.day', $request->dayName)
        ->select('staff_global_days.day', 'staff_global_hours.start_time', 'staff_global_hours.end_time', 'staff_global_hours.id')
        ->get();
    } else {
      $information['global_time'] = StaffGlobalHour::join('admin_global_days', 'admin_global_days.id', '=', 'staff_global_hours.global_day_id')
        ->leftJoin('service_bookings', function ($join) use ($request, $bookedCount) {
          $join->on('service_bookings.service_hour_id', '=', 'staff_global_hours.id')
            ->where('service_bookings.booking_date', '=', $request->bookingDate)
            ->where(function ($query) use ($bookedCount) {
              $query->where('staff_global_hours.max_booking', '<=', $bookedCount);
            })
            ->where('service_bookings.order_status', '!=', 'rejected')
            ->where('service_bookings.staff_id', '=', $request->staff_id);
        })
        ->whereNull('service_bookings.id')
        ->where('staff_global_hours.vendor_id', $request->vendor_id)
        ->where('admin_global_days.day', $request->dayName)
        ->select('admin_global_days.day', 'staff_global_hours.start_time', 'staff_global_hours.end_time', 'staff_global_hours.id')
        ->get();
    }

    $service = Services::findOrFail($request->serviceId);
    if (!empty($service->max_person)) {
      $information['maxPerson'] = $service->max_person;
    }
    return view('frontend.services.booking-modal.time', $information);
  }

  //show staff holiday
  public function staffHoliday($id)
  {
    $staff = Staff::find($id);
    $vendor_id = $staff->vendor_id;

    if (Session::has('serviceData')) {
      $service = Session::get('serviceData');
    }

    $holiday = StaffHoliday::where('staff_id', $id)
      ->where('vendor_id', $vendor_id)
      ->get();

    $globalWeekend = StaffGlobalDay::where('is_weekend', 1)
      ->where('vendor_id', $vendor_id)
      ->get();

    $adminGlobalWeekend = AdminGlobalDay::where('is_weekend', 1)
      ->get();

    $staffWeekend = StaffDay::where('is_weekend', 1)
      ->where('staff_id', $id)
      ->where('vendor_id', $vendor_id)
      ->get();

    $serviceHoure = StaffServiceHour::where('staff_id', $id)
      ->get();

    $globalHoliday = StaffGlobalHoliday::where('vendor_id', $vendor_id)
      ->select('date')
      ->get();

    $array = [
      'holiday' => $holiday,
      'globalHoliday' => $globalHoliday,
      'staffWeekend' => $staffWeekend,
      'globalWeekend' => $globalWeekend,
      'adminGlobalWeekend' => $adminGlobalWeekend,
      'serviceHoure' => $serviceHoure,
      'vendor_id' => $vendor_id,
      'serviceId' => $service['service_id'],
    ];

    return $array;
  }

  //curstomer login
  public function login(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    if (Basic::recaptchaEnabled()) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [];

    if (Basic::recaptchaEnabled()) {
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()
      ], 400);
    }

    $login = trim((string) $request->input('username'));
    $credentials = [
      filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $login,
      'password' => $request->input('password'),
    ];

    // login attempt
    if (Auth::guard('web')->attempt($credentials)) {
      $authUser = Auth::guard('web')->user();

      if ($authUser->email_verified_at == null) {
        Auth::guard('web')->logout();

        return response()->json(['error' => 'Please verify your email address']);
      }
      if ($authUser->status == 0) {
        Auth::guard('web')->logout();

        return response()->json(['error' => 'Sorry, your account has been deactivated']);
      }
      $billingData = [
        'name' => $authUser->name,
        'email' => $authUser->email,
        'address' => $authUser->address,
        'zip_code' => $authUser->zip_code,
        'country' => $authUser->country,
        'user_id' => $authUser->id,
        'phone' => $authUser->phone,
      ];
      return response()->json([
        'success' => 'Login successful',
        'billingData' => $billingData,
      ]);
    } else {
      return response()->json(['error' => 'Incorrect username or password']);
    }
  }

  public function signup(Request $request)
  {
    $rules = [
      'name' => 'required|string|max:255',
      'username' => 'required|string|max:255|unique:users,username',
      'email' => 'required|email:rfc,dns|max:255|unique:users,email',
      'phone' => 'required|string|max:255',
      'password' => 'required|confirmed',
      'password_confirmation' => 'required',
      'address' => 'nullable|string|max:255',
      'city' => 'nullable|string|max:255',
      'zip_code' => 'nullable|string|max:255',
    ];

    if (Basic::recaptchaEnabled()) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [
      'email.unique' => 'This email or username is already registered. Please login instead.',
      'username.unique' => 'This email or username is already registered. Please login instead.',
      'password_confirmation.required' => 'The confirm password field is required.',
    ];

    if (Basic::recaptchaEnabled()) {
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->getMessageBag(),
      ], 422);
    }

    $user = new User();
    $user->name = $request->name;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address;
    $user->city = $request->city;
    $user->zip_code = $request->zip_code;
    $user->status = 1;
    $user->email_verified_at = Carbon::now();
    $user->password = Hash::make($request->password);
    $user->save();

    Auth::guard('web')->login($user);

    $billingData = [
      'name' => $user->name,
      'email' => $user->email,
      'address' => $user->address,
      'zip_code' => $user->zip_code,
      'country' => $user->country,
      'user_id' => $user->id,
      'phone' => $user->phone,
    ];

    return response()->json([
      'success' => 'Account created successfully',
      'billingData' => $billingData,
    ]);
  }

  public function sessionForget()
  {
    Log::info('Clearing service booking success/payment session data', [
      'session_id' => session()->getId(),
      'complete' => Session::get('complete'),
      'payment_info_id' => optional(Session::get('paymentInfo'))->id,
      'service_data_present' => Session::has('serviceData'),
    ]);

    Session::forget('complete');
    Session::forget('paymentInfo');
    Session::forget('serviceData');
  }

  //staff search
  public function staffSearch(Request $request, $id)
  {
    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();
    $vendor_id = Services::where('id', $id)->pluck('vendor_id')->first();
    // Initial query to find staff based on service ID and other criteria
    $staffs = Staff::join('staff_contents', 'staff.id', '=', 'staff_contents.staff_id')
      ->join('staff_services', 'staff.id', '=', 'staff_services.staff_id')
      ->where('staff_services.service_id', $id)
      ->where('staff_contents.language_id', $language->id)
      ->where(function ($query) use ($request) {
        $query->where('staff_contents.name', 'like', '%' . $request->searchVal . '%')
          ->orWhere('staff.email', 'like', '%' . $request->searchVal . '%')
          ->orWhere('staff_contents.location', 'like', '%' . $request->searchVal . '%');
      })
      ->where('status', 1)
      ->select(
        'staff.id',
        'staff.email',
        'staff.image',
        'staff.is_day',
        'staff_contents.name',
        'staff_contents.staff_id'
      )
      ->get();
    return view('frontend.services.booking-modal.staff-search-data', compact('staffs'));
  }


  //customer message
  public function message(Request $request)
  {
    try {
      $rules = [
        'first_name' => 'required',
        'email' => 'required',
        'message' => 'required'
      ];

      $message = [
        'first_name.required' => 'The name field is required.',
        'email.required' => 'The email field is required.',
        'message.required' => 'The message field is required.',
      ];

      $validator = Validator::make($request->all(), $rules, $message);

      if ($validator->fails()) {
        return redirect()->back()
          ->withErrors($validator)
          ->withInput()
          ->with('error', 'Field is missing !');
      }

      $bookingInfo = InqueryMessage::create([
        'vendor_id' => $request->vendor_id,
        'service_id' => $request->service_id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'message' => $request->message,
      ]);

      $bs = DB::table('basic_settings')->select('to_mail')->first();
      $admin = Admin::whereNull('role_id')->select('email', 'username')->first();
      if ($bs->to_mail != null) {
        $admin_email = $bs->to_mail;
      } else {
        $admin_email = $admin->email;
      }

      if ($request->vendor_id != 0) {
        $to_mail = DB::table('vendors')
          ->where('id', $request->vendor_id)
          ->select('recived_email')->first();
        $vendor_mail =  $to_mail->recived_email;
      } else {
        $vendor_mail = $admin_email;
      }

      // get the mail template info from db
      $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'service_inquery')->first();
      $mailData['subject'] = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;

      // get the website title info from db
      $info = Basic::select('website_title')->first();

      $name = $request->first_name . '' . $request->last_name;
      $message = $request->message;
      $email = $bookingInfo->email;
      $service_name = $bookingInfo->service->name;
      $url = route('frontend.service.details', ['slug' => $bookingInfo->service->slug, 'id' => $bookingInfo->service_id]);

      if ($request->vendor_id != 0) {
        $vendor_name = $bookingInfo->vendor->name;
      } else {
        $vendor_name = $admin->username;
      }
      $websiteTitle = $info->website_title;
      // replacing with actual data
      $mailBody = str_replace('{username}', $vendor_name, $mailBody);
      $mailBody = str_replace('{enquirer_name}', "<a href=" . $url . ">$service_name</a>", $mailBody);
      $mailBody = str_replace('{service_name}', $service_name, $mailBody);
      $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);
      $mailBody = str_replace('{enquirer_email}', $email, $mailBody);
      $mailBody = str_replace('{enquirer_message}', $message, $mailBody);

      $mailData['body'] = $mailBody;

      $mailData['recipient'] = $vendor_mail;

      BasicMailer::sendMail($mailData);

      Session::flash('message', 'Message sent successfully');
      Session::flash('alert-type', 'success');
      return redirect()->back();
    } catch (\Exception $e) {
      Session::flash('message', 'Something went wrong.');
      Session::flash('alert-type', 'error');
      return redirect()->back();
    }
  }


  //review store
  public function storeReview(Request $request, $id)
  {
    Session::flash('error', 'Reviews can only be submitted from the secure review link sent after a completed booking.');
    return redirect()->back();
  }

  //payment success message
  public function paymentSuccess($id)
  {
    Log::info('Service payment success modal requested', [
      'host' => request()->getHost(),
      'session_id' => session()->getId(),
      'requested_service_id' => $id,
      'complete' => Session::get('complete'),
      'payment_info_id' => optional(Session::get('paymentInfo'))->id,
    ]);

    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();
    $language_id = $language->id;

    $information['staffs'] = Staff::join('staff_contents', 'staff.id', '=', 'staff_contents.staff_id')
      ->join('staff_services', 'staff.id', '=', 'staff_services.staff_id')
      ->where('staff_services.service_id', $id)
      ->where('staff_contents.language_id', $language->id)
      ->where('status', 1)
      ->select(
        'staff.is_day',
        'staff.id',
        'staff.email',
        'staff.image',
        'staff_contents.name',
        'staff.vendor_id as vendor_id',
        'staff_contents.staff_id',
      )
      ->get();

    $information['bs'] = Basic::settings(['google_recaptcha_status']);
    $information['authUser'] = Auth::guard('web')->check() == true ? Auth::guard('web')->user() : null;
    $information['admin'] = Admin::whereNull('role_id')->first();


    $information['online_gateways'] = OnlineGateway::where('status', 1)->get();
    $information['offline_gateways'] = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();

    $appointment  = Session::get('paymentInfo');
    if (!$appointment || !isset($appointment->id)) {
      Log::warning('Service payment success modal missing paymentInfo session', [
        'session_id' => session()->getId(),
        'requested_service_id' => $id,
        'complete' => Session::get('complete'),
      ]);

      abort(404);
    }

    $information['bookingInfo'] = ServiceBooking::with(['serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->findOrFail($appointment->id);

    Log::info('Service payment success modal loaded booking info', [
      'session_id' => session()->getId(),
      'booking_id' => $information['bookingInfo']->id,
      'service_id' => $information['bookingInfo']->service_id,
      'payment_status' => $information['bookingInfo']->payment_status,
    ]);

    return view('frontend.services.booking-modal.service-modal', $information);
  }

  //billing form
  public function billing(Request $request)
  {
    $rules = [
      'name' => 'required',
      'phone' => 'required',
      'email' => 'required|email',
      'address' => 'required',
      'billing_same' => 'required|in:0,1',
    ];

    if ($request->input('billing_same', 1) == 0) {
      $rules = array_merge($rules, [
        'billing_name_alt' => 'required',
        'billing_phone_alt' => 'required',
        'billing_email_alt' => 'required|email',
        'billing_address_alt' => 'required',
      ]);
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()
      ], 400);
    }

    $billingSame = (int) $request->input('billing_same', 1);

    $siteData = [
      'name' => $request->name,
      'phone' => $request->phone,
      'email' => $request->email,
      'address' => $request->address,
      'zip_code' => $request->zip_code,
      'country' => $request->country,
    ];

    $differentBillingData = [
      'name' => $request->billing_name_alt,
      'phone' => $request->billing_phone_alt,
      'email' => $request->billing_email_alt,
      'address' => $request->billing_address_alt,
      'zip_code' => $request->billing_zip_alt,
      'country' => $request->billing_country_alt,
    ];

    $billingData = $billingSame ? $siteData : $differentBillingData;
    $alternateData = $billingSame ? null : $differentBillingData;

    $data = [
      'name' => $request->name,
      'phone' => $request->phone,
      'email' => $request->email,
      'address' => $request->address,
      'zip_code' => $request->zip_code,
      'country' => $request->country,
      'booking_date' => $request->booking_date,
      'start_time' => $request->input('start_time'),
      'end_time' => $request->input('end_time'),
      'user_id' => $request->user_id,
      'staff_id' => $request->staff_id ?? 11, // Set default staff ID
      'service_hour_id' => $request->service_hour_id,
      'max_person' => $request->max_person,
      'billing_same' => $billingSame,
      'billing' => $billingData,
      'site' => $siteData,
      'alternate' => $alternateData,
    ];

    if (!Session::has('serviceData')) {
      return Response::json([
        'errors' => [
          'total_price' => ['Your booking session expired. Please reopen the booking form and try again.']
        ]
      ], 422);
    }

    $serviceData = Session::get('serviceData');
    $priceCheck = $this->resolveBookingPrice($serviceData, $request, $siteData);
    $clientTotal = round((float) $request->input('total_price', 0), 2);

    if (!$priceCheck['ok'] || abs($clientTotal - $priceCheck['total']) > 0.01) {
      Log::warning('Billing price validation failed', [
        'session_id' => $request->session()->getId(),
        'service_id' => $serviceData['service_id'] ?? null,
        'client_total' => $clientTotal,
        'server_total' => $priceCheck['total'],
        'price_parts' => $priceCheck['parts'],
        'reason' => $priceCheck['message'],
      ]);

      return Response::json([
        'errors' => [
          'total_price' => [$priceCheck['message']]
        ],
        'server_total' => $priceCheck['total'],
      ], 422);
    }

    // Update serviceData in session with the server-validated price.
    if ($request->has('total_price')) {
      $serviceData = Session::get('serviceData');
      $serviceData['service_ammount'] = number_format($priceCheck['total'], 2, '.', '');
      $serviceData['question_answers'] = $request->input('question_answers', '{}');
      $serviceData['urgency_surcharge_amount'] = $priceCheck['parts']['urgency'];
      $serviceData['urgency_surcharge_period'] = $priceCheck['urgency_period'];
      $serviceData['billing_same'] = $billingSame;
      $serviceData['site_name']     = $siteData['name'];
      $serviceData['site_phone']    = $siteData['phone'];
      $serviceData['site_email']    = $siteData['email'];
      $serviceData['site_address']  = $siteData['address'];
      $serviceData['site_zip_code'] = $siteData['zip_code'];
      $serviceData['site_country']  = $siteData['country'];
      Session::put('serviceData', $serviceData);

      Log::info('Billing step updated serviceData session', [
        'session_id' => $request->session()->getId(),
        'service_id' => $serviceData['service_id'] ?? null,
        'service_amount' => $serviceData['service_ammount'] ?? null,
        'billing_same' => $billingSame,
      ]);
    }

    return response()->json($data);
  }

  private function resolveBookingPrice(array $serviceData, Request $request, array $billingData): array
  {
    $service = Services::query()->find($serviceData['service_id'] ?? null);

    if (!$service) {
      return [
        'ok' => false,
        'total' => 0.00,
        'parts' => $this->emptyPriceParts(),
        'urgency_period' => null,
        'message' => 'The selected service is no longer available. Please refresh and try again.',
      ];
    }

    $answers = json_decode($request->input('question_answers', '{}'), true);
    if (!is_array($answers)) {
      $answers = [];
    }

    $basePrice = round((float) $service->price, 2);
    $questionPrice = $this->calculateQuestionPrice((int) $service->id, $answers);
    $postalPrice = $this->calculatePostalPrice($billingData['zip_code'] ?? null, [
      'service_id' => $service->id,
      'service_category_id' => $this->serviceCategoryId((int) $service->id),
    ]);
    $urgency = $this->calculateUrgencySurcharge($request);

    $total = round($basePrice + $questionPrice + $postalPrice + $urgency['amount'], 2);

    if ($total <= 0) {
      return [
        'ok' => false,
        'total' => $total,
        'parts' => [
          'base' => $basePrice,
          'questions' => $questionPrice,
          'postal' => $postalPrice,
          'urgency' => $urgency['amount'],
        ],
        'urgency_period' => $urgency['period'],
        'message' => 'The booking total must be greater than zero before payment.',
      ];
    }

    return [
      'ok' => true,
      'total' => $total,
      'parts' => [
        'base' => $basePrice,
        'questions' => $questionPrice,
        'postal' => $postalPrice,
        'urgency' => $urgency['amount'],
      ],
      'urgency_period' => $urgency['period'],
      'message' => 'The price changed while you were booking. Please review the updated total and try again.',
    ];
  }

  private function emptyPriceParts(): array
  {
    return [
      'base' => 0.00,
      'questions' => 0.00,
      'postal' => 0.00,
      'urgency' => 0.00,
    ];
  }

  private function calculateQuestionPrice(int $serviceId, array $answers): float
  {
    $questions = Question::query()
      ->with('options')
      ->where('service_id', $serviceId)
      ->get()
      ->keyBy('id');

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

      if (!in_array($question->type, ['select', 'multi'])) {
        continue;
      }

      if ($answer === null || $answer === '') {
        continue;
      }

      $selectedValues = is_array($answer) ? $answer : [$answer];
      $selectedValues = array_map('strval', $selectedValues);

      $options = $question->relationLoaded('options')
        ? $question->getRelation('options')
        : $question->options()->get();

      foreach (($options ?? collect()) as $option) {
        if (in_array((string) $option->option_text, $selectedValues, true)) {
          $total += (float) $option->price;
        }
      }
    }

    return round($total, 2);
  }

  private function calculatePostalPrice(?string $zipCode, array $context = []): float
  {
    $zipCode = trim((string) $zipCode);

    if ($zipCode === '') {
      return 0.00;
    }

    $surcharge = PostalCode::surchargeFor($zipCode, $context);

    if ($surcharge) {
      return round((float) $surcharge['price'], 2);
    }

    $postalPrice = DB::table('basic_settings')
      ->where('uniqid', 12345)
      ->value('postal_code_default_price');

    return round((float) ($postalPrice ?? 0), 2);
  }

  private function calculateUrgencySurcharge(Request $request): array
  {
    $bookingDate = $request->input('booking_date');
    $startTime = $request->input('start_time');
    $timezone = $this->bookingTimezone();
    $calculatedAtInput = $request->input('urgency_calculated_at');
    $bookingStartsAtInput = $request->input('urgency_booking_starts_at');

    if (empty($bookingDate) || empty($startTime)) {
      return ['amount' => 0.00, 'period' => null];
    }

    try {
      $bookingStartsAt = !empty($bookingStartsAtInput)
        ? Carbon::parse($bookingStartsAtInput)
        : Carbon::parse($bookingDate . ' ' . $startTime, $timezone);
      $calculatedAt = !empty($calculatedAtInput)
        ? Carbon::parse($calculatedAtInput)
        : now($timezone);
      $hoursUntilBooking = ((float) $bookingStartsAt->format('U.u') - (float) $calculatedAt->format('U.u')) / 3600;
    } catch (\Exception $e) {
      return ['amount' => 0.00, 'period' => null];
    }

    if ($hoursUntilBooking >= 0 && $hoursUntilBooking <= 4) {
      return ['amount' => 150.00, 'period' => 'Within 4 hours'];
    }

    if ($hoursUntilBooking >= 0 && $hoursUntilBooking <= 24) {
      return ['amount' => 100.00, 'period' => 'Within 24 hours'];
    }

    if ($hoursUntilBooking >= 0 && $hoursUntilBooking <= 48) {
      return ['amount' => 50.00, 'period' => 'Within 48 hours'];
    }

    return ['amount' => 0.00, 'period' => null];
  }

  private function bookingTimezone(): string
  {
    $timezone = Basic::query()->value('timezone') ?: config('app.timezone', 'UTC');

    try {
      new \DateTimeZone($timezone);
    } catch (\Exception $e) {
      $timezone = config('app.timezone', 'UTC');
    }

    return $timezone;
  }

  public function postalCodePrice(Request $request)
  {
    $zipCode = trim($request->input('zip_code', ''));

    if ($zipCode === '') {
      return response()->json([
        'found' => false,
        'postal_price' => 0,
        'total_surcharge' => 0,
        'items' => [],
      ], 200);
    }

    $cartItems = collect(Session::get('serviceCart', []));

    if ($cartItems->isNotEmpty()) {
      $items = $this->cartPostcodeSurcharges($zipCode, $cartItems);
      $totalSurcharge = round(collect($items)->sum('surcharge'), 2);

      return response()->json([
        'found' => $totalSurcharge > 0,
        'postal_price' => $totalSurcharge,
        'total_surcharge' => $totalSurcharge,
        'items' => $items,
      ], 200);
    }

    $context = [];
    if ($request->filled('service_id')) {
      $context['service_id'] = (int) $request->input('service_id');
      $context['service_category_id'] = $this->serviceCategoryId((int) $request->input('service_id'));
    }
    if ($request->filled('service_package_id')) {
      $context['service_package_id'] = (int) $request->input('service_package_id');
    }

    $surcharge = PostalCode::surchargeFor($zipCode, $context);

    if ($surcharge) {
      return response()->json([
        'found' => true,
        'postal_price' => (float) $surcharge['price'],
        'total_surcharge' => (float) $surcharge['price'],
        'items' => [],
      ], 200);
    }

    $defaultPrice = DB::table('basic_settings')
      ->where('uniqid', 12345)
      ->value('postal_code_default_price');

    return response()->json([
      'found' => false,
      'postal_price' => $defaultPrice !== null ? (float) $defaultPrice : 0,
      'total_surcharge' => 0,
      'items' => [],
    ], 200);
  }

  private function cartPostcodeSurcharges(string $zipCode, $cartItems): array
  {
    $serviceIds = $cartItems
      ->filter(function ($item) {
        return ($item['item_type'] ?? 'service') === 'service';
      })
      ->pluck('service_id')
      ->filter()
      ->unique()
      ->values();
    $serviceCategories = ServiceContent::query()
      ->whereIn('service_id', $serviceIds)
      ->pluck('category_id', 'service_id');

    return $cartItems->map(function ($item) use ($zipCode, $serviceCategories) {
      $context = [];

      if (($item['item_type'] ?? 'service') === 'package') {
        $context['service_package_id'] = (int) ($item['package_id'] ?? 0);
      } else {
        $context['service_id'] = (int) ($item['service_id'] ?? 0);
        $context['service_category_id'] = (int) ($serviceCategories[$item['service_id'] ?? null] ?? 0);
      }

      $surcharge = PostalCode::surchargeFor($zipCode, $context);

      return [
        'key' => $item['key'] ?? null,
        'surcharge' => $surcharge ? (float) $surcharge['price'] : 0.00,
        'rule' => $surcharge,
      ];
    })->values()->all();
  }

  private function serviceCategoryId(int $serviceId): ?int
  {
    return ServiceContent::query()
      ->where('service_id', $serviceId)
      ->value('category_id');
  }

  //show general holiday and availability for service
  public function generalHoliday($serviceId)
  {
    $service = Services::find($serviceId);
    $vendor_id = $service->vendor_id;

    // Store service data in session for later use
    Session::put('serviceData', [
      'service_id' => $serviceId,
      'vendor_id' => (int) ($service->vendor_id ?? 0),
      'service_ammount' => $service->price,
      'zoom_status' => $service->zoom_meeting ?? 0,
      'calendar_status' => $service->calendar_status ?? 0,
    ]);

    // Get global holidays for the vendor
    $globalHoliday = StaffGlobalHoliday::where('vendor_id', $vendor_id)
      ->select('date')
      ->get();

    // Get global weekends for the vendor
    $globalWeekend = StaffGlobalDay::where('is_weekend', 1)
      ->where('vendor_id', $vendor_id)
      ->get();

    // Get admin global weekends if vendor is admin
    $adminGlobalWeekend = AdminGlobalDay::where('is_weekend', 1)
      ->get();

    $array = [
      'holiday' => collect(), // Empty collection since we're not using staff holidays
      'globalHoliday' => $globalHoliday,
      'staffWeekend' => collect(), // Empty collection since we're not using staff weekends
      'globalWeekend' => $globalWeekend,
      'adminGlobalWeekend' => $adminGlobalWeekend,
      'serviceHoure' => collect(), // Empty collection since we're using global hours
      'vendor_id' => $vendor_id,
      'serviceId' => $serviceId,
    ];

    return $array;
  }

  //show general time slots for a specific date
  public function showGeneralHour(Request $request, $serviceId)
  {
    $service = Services::find($serviceId);
    $vendor_id = $service->vendor_id;
    if (!$vendor_id) {
        $vendor_id = 0;
    }

    // Debug logging
    \Log::info('showGeneralHour called', [
      'serviceId' => $serviceId,
      'vendor_id' => $vendor_id,
      'dayName' => $request->dayName,
      'bookingDate' => $request->bookingDate
    ]);

    // Get all available global time slots for the selected day
    $globalTimeSlots = StaffGlobalHour::join('admin_global_days', 'admin_global_days.id', '=', 'staff_global_hours.global_day_id')
      ->where('staff_global_hours.vendor_id', $vendor_id)
      ->where('admin_global_days.day', $request->dayName)
      ->select('admin_global_days.day', 'staff_global_hours.start_time', 'staff_global_hours.end_time', 'staff_global_hours.id', 'staff_global_hours.max_booking')
      ->get();

    // If no global time slots, try admin global time slots
    if ($globalTimeSlots->isEmpty() && $vendor_id == 0) {
      $globalTimeSlots = StaffGlobalHour::join('admin_global_days', 'admin_global_days.id', '=', 'staff_global_hours.global_day_id')
        ->where('staff_global_hours.vendor_id', 0)
        ->where('admin_global_days.day', $request->dayName)
        ->select('admin_global_days.day', 'staff_global_hours.start_time', 'staff_global_hours.end_time', 'staff_global_hours.id', 'staff_global_hours.max_booking')
        ->get();
    }

    // If still no time slots, create default ones (8AM-6PM in 2-hour intervals)
    if ($globalTimeSlots->isEmpty()) {
      $defaultTimeSlots = [
        ['start_time' => '08:00:00', 'end_time' => '10:00:00'],
        ['start_time' => '10:00:00', 'end_time' => '12:00:00'],
        ['start_time' => '12:00:00', 'end_time' => '14:00:00'],
        ['start_time' => '14:00:00', 'end_time' => '16:00:00'],
        ['start_time' => '16:00:00', 'end_time' => '18:00:00'],
      ];

      foreach ($defaultTimeSlots as $timeSlot) {
        $slot = new \stdClass();
        $slot->id = 'default_' . str_replace(':', '', $timeSlot['start_time']);
        $slot->start_time = $timeSlot['start_time'];
        $slot->end_time = $timeSlot['end_time'];
        $slot->max_booking = 10; // Default max booking per slot
        $globalTimeSlots->push($slot);
      }
    }

    // All configured slots are shown regardless of booking count
    $availableSlots = $globalTimeSlots->all();

    // Debug logging
    \Log::info('showGeneralHour result', [
      'globalTimeSlots_count' => $globalTimeSlots->count(),
      'availableSlots_count' => count($availableSlots),
      'availableSlots' => $availableSlots
    ]);

    return view('frontend.services.booking-modal.time-slots', compact('availableSlots'));
  }
}
