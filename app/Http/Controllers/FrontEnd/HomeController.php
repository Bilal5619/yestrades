<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Models\Admin;
use App\Models\Admin\HeroSliderImage;
use App\Models\Admin\SectionContent;
use App\Models\BasicSettings\AboutUs;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\SocialMedia;
use App\Models\CustomSection;
use App\Models\Footer\FooterContent;
use App\Models\Footer\QuickLink;
use App\Models\HomePage\Banner;
use App\Models\HomePage\Section;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServicePackage;
use App\Models\Services\Services;
use App\Models\Popup;
use App\Models\Vendor;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
  public function index()
  {
    $themeVersion = Basic::query()->pluck('theme_version')->first();

    $secInfo = Section::query()->first();
    $queryResult['secInfo'] = $secInfo;

    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['language'] = $language;
    $queryResult['currentLanguageInfo'] = $language;
    $queryResult['allLanguageInfos'] = \App\Models\Language::all();
    $queryResult['popupInfos'] = Popup::where('status', 1)->get();
    $queryResult['socialMediaInfos'] = SocialMedia::query()->orderBy('serial_number', 'asc')->get();
    $queryResult['menuInfos'] = optional($language->menuInfo)->menus ?? json_encode([]);
    $queryResult['cookieAlertInfo'] = $language->cookieAlertInfo()->first();
    $queryResult['footerSectionStatus'] = Section::query()->pluck('footer_section_status')->first();
    $queryResult['footerInfo'] = $queryResult['footerSectionStatus'] == 1 ? $language->footerContent()->first() : null;
    $queryResult['quickLinkInfos'] = $queryResult['footerSectionStatus'] == 1
      ? $language->footerQuickLink()->orderBy('serial_number', 'asc')->get()
      : [];
    $queryResult['cartItemInfo'] = session()->get('productCart', []);


    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_home', 'meta_description_home')->first();

    if ($secInfo->about_section_status == 1) {
      $queryResult['aboutSecInfo'] = $language->aboutSection()->first();
    }
    $queryResult['sectionContent'] = SectionContent::where('language_id', $language->id)->first();
    $queryResult['quickLinkInfo'] = QuickLink::where('language_id', $language->id)->first();

    //footer section
    $queryResult['footer_logo'] = DB::table('basic_settings')->select('footer_logo')->first();
    $queryResult['footerContent'] = FooterContent::where('language_id', $language->id)->first();

    if ($themeVersion == 1) {
      $queryResult['processes'] = $language->workProcess()->orderBy('serial_number', 'asc')->get();
    }

    if ($themeVersion == 2 || $themeVersion == 3) {
      $queryResult['banners'] = Banner::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();
    }
    if ($themeVersion == 3) {
      $processes = $language->workProcess()->orderBy('serial_number', 'asc')->get();
      $half = ceil($processes->count() / 2);
      $queryResult['FirstWorkProcess'] = $processes->slice(0, $half);
      $queryResult['secondProcesses'] = $processes->slice($half);
    }

    if ($themeVersion == 2) {
      $queryResult['processes'] = $language->workProcess()->orderBy('serial_number', 'asc')->get();
    }

    $queryResult['currencyInfo'] = $this->getCurrencyInfo();
    $queryResult['basicInfo'] = Basic::first();
    $queryResult['websiteInfo'] = $queryResult['basicInfo'];

    if ($secInfo->testimonial_section_status == 1) {
      $queryResult['testimonials'] = $language->testimonial()->orderByDesc('id')->get();
      $queryResult['total_testimonial'] = $language->testimonial()->count();
    }

    $vendorStatus = Vendor::where('status', 1)->select('id')->get()->toArray();

    $queryResult['featured_services'] = Services::join('service_promotions', 'service_promotions.service_id', '=', 'services.id')
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
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_contents.address',
        'service_categories.name as categoryName',
        'service_categories.id as categoryId',
      )
      ->inRandomOrder()
      ->get();

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

    $queryResult['renewalItems'] = $renewalServices
      ->concat($renewalPackages)
      ->sortBy([
        ['renewal_sort_order', 'asc'],
        ['id', 'desc'],
      ])
      ->values();

    //services
    $perPage = ($themeVersion != 3) ? 8 : 6;
    $queryResult['services'] = Services::join('service_contents', 'services.id', '=', 'service_contents.service_id')
      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
      ->where('service_contents.language_id', $language->id)
      ->where('service_categories.language_id', $language->id)
      ->where('services.status', 1)
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
      ->select(
        'services.*',
        'service_contents.name',
        'service_contents.slug',
        'service_contents.address',
        'service_categories.name as categoryName',
        'service_categories.id as categoryId'
      )
      ->orderBy('services.id', 'desc')
      ->paginate($perPage);

    //admin info
    $queryResult['admin'] = Admin::whereNull('role_id')->firstOrFail();

    $categories = ServiceCategory::has('service_content')
      ->where('language_id', $language->id)
      ->where('status', 1)
      ->orderBy('serial_number', 'asc')
      ->get();


    //home page category wise service total count
    $vendorStatus = \App\Models\Vendor::where('status', 1)->select('id')->get()->toArray();
    foreach ($categories as $category) {
      $serviceCount = Services::join(
        'service_contents',
        'service_contents.service_id',
        '=',
        'services.id'
      )
        ->where(function ($query) use ($vendorStatus) {
          $query->whereIn('services.vendor_id', $vendorStatus)->orWhere('services.vendor_id', 0);
        })
        ->where('services.status', 1)
        ->where('service_contents.category_id', $category->id)
        ->count();

      // Add service count to the category object
      $category->service_count = $serviceCount;
    }

    $queryResult['categories'] = $categories;


    //all vendors
    $baseVendorQqury = Vendor::join('memberships', 'memberships.vendor_id', 'vendors.id')
      ->join('vendor_infos', 'vendors.id', '=', 'vendor_infos.vendor_id')
      ->where([
        ['memberships.status', 1],
        ['memberships.start_date', '<=', Carbon::now()->format('Y-m-d')],
        ['memberships.expire_date', '>=', Carbon::now()->format('Y-m-d')],
      ])
      ->where('vendors.status', 1)
      ->where('vendor_infos.language_id', $language->id)
      ->where('vendors.id', '!=', 0);

    $totalVendor = (clone $baseVendorQqury)->count();
    $featuredVendors = (clone $baseVendorQqury)
      ->where('vendors.featured', 1)
      ->select('vendors.*', 'vendors.id as vendorId', 'memberships.*', 'vendor_infos.name as vendorName')
      ->get();

    foreach ($featuredVendors as $vendor) {
      $serviceCount = Services::where('status', 1)
        ->where('vendor_id', $vendor->vendorId)
        ->count();

      // Add service count to the category object
      $vendor->total_service = $serviceCount;
    }
    $queryResult['featuredVendors'] = $featuredVendors;
    $queryResult['vendors'] = $totalVendor;

    //Strip payment
    $stripe = OnlineGateway::where('keyword', 'stripe')->first();
    $stripe_info = json_decode($stripe->information, true);
    $queryResult['stripe_key'] = $stripe_info['key'];

    //Authorize.Net payment
    $authorizeNet = OnlineGateway::where('keyword', 'authorize.net')->first();
    $authorizeInfo = json_decode($authorizeNet->information, true);

    if ($authorizeInfo['sandbox_check'] == 1) {
      $queryResult['authorizeUrl'] = 'https://jstest.authorize.net/v1/Accept.js';
    } else {
      $queryResult['authorizeUrl'] = 'https://js.authorize.net/v1/Accept.js';
    }

    $queryResult['authorize_login_id'] = $authorizeInfo['login_id'];
    $queryResult['authorize_public_key'] = $authorizeInfo['public_key'];


    $pageType = 'home';
    $sections = [
      'hero_section',
      'category_section',
      'about_section',
      'features_section',
      'work_process_section',
      'testimonial_section',
      'featured_service_section',
      'call_to_action_section',
      'vendor_section',
      'latest_service_section',
      'footer_section',
      'banner_section'
    ];

    foreach ($sections as $section) {
      $queryResult["after_" . str_replace('_section', '', $section)] = CustomSection::where('order', $section)
        ->where('page_type', $pageType)
        ->orderBy('serial_number','asc')
        ->get();
    }

    $sectionInfo = Section::select('custom_section_status')->first();
    if (!empty($sectionInfo->custom_section_status)) {
      $info = json_decode($sectionInfo->custom_section_status, true);
      $queryResult['homecusSec'] = $info;
    }


    $queryResult['heroSliderImages'] = HeroSliderImage::orderBy('sort_order')->get();
    $heroSlideServiceIds = $queryResult['heroSliderImages']
      ->pluck('service_id')
      ->filter()
      ->map(fn ($id) => (int) $id)
      ->unique()
      ->values();

    $queryResult['heroSlideServices'] = $heroSlideServiceIds->isNotEmpty()
      ? Services::join('service_contents', 'services.id', '=', 'service_contents.service_id')
        ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
        ->whereIn('services.id', $heroSlideServiceIds)
        ->where('service_contents.language_id', $language->id)
        ->where('service_categories.language_id', $language->id)
        ->where('services.status', 1)
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
        ->select(
          'services.*',
          'service_contents.name',
          'service_contents.slug',
          'service_contents.address',
          'service_contents.features',
          'service_categories.name as categoryName',
          'service_categories.id as categoryId'
        )
        ->get()
        ->keyBy('id')
      : collect();

    if ($themeVersion == 1) {
      return view('frontend.home.index-v1', $queryResult);
    } elseif ($themeVersion == 2) {
      return view('frontend.home.index-v2', $queryResult);
    } elseif ($themeVersion == 3) {
      return view('frontend.home.index-v3', $queryResult);
    }
  }


  //about
  public function about()
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keywords_about_page', 'meta_description_about_page')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();
    $secInfo = Section::query()->first();
    $queryResult['secInfo'] = $secInfo;

    if ($secInfo->work_process_section_status == 1) {
      $queryResult['sectionContent'] = SectionContent::where('language_id', $language->id)->select('workprocess_section_title', 'workprocess_section_subtitle', 'workprocess_section_btn', 'workprocess_section_url', 'workprocess_icon', 'testimonial_section_image', 'testimonial_section_title', 'testimonial_section_subtitle', 'testimonial_section_clients')->first();
      $queryResult['processes'] = $language->workProcess()->orderBy('serial_number', 'asc')->get();
    }

    $queryResult['about'] = AboutUs::where('language_id', $language->id)->first();
    $queryResult['features'] = $language->features()->orderBy('serial_number', 'asc')->get();
    $queryResult['total_testimonial'] = $language->testimonial()->count();

    if ($secInfo->testimonial_section_status == 1) {
      $queryResult['testimonials'] = $language->testimonial()->orderByDesc('id')->get();
    }

    $pageType = 'about';
    $sections = ['about_section', 'features_section', 'work_process_section', 'testimonial_section'];

    foreach ($sections as $section) {
      $queryResult["after_" . str_replace('_section', '', $section)] = CustomSection::where('order', $section)
        ->where('page_type', $pageType)
        ->orderBy('serial_number', 'asc')
        ->get();
    }

    $sectionInfo = Section::select('about_custom_section_status')->first();
    if(!empty($sectionInfo->about_custom_section_status)){
      $info = json_decode($sectionInfo->about_custom_section_status,true);
      $queryResult['aboutSec'] = $info;
    }


    return view('frontend.about', $queryResult);
  }

  //offline
  public function offline()
  {
    return view('frontend.offline');
  }
}
