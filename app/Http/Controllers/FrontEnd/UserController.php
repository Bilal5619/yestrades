<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Helpers\BasicMailer;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Services\ServiceBooking;
use App\Models\Services\Services;
use App\Models\Services\Wishlist;
use App\Models\Shop\ProductOrder;
use App\Models\User;
use App\Rules\MatchEmailRule;
use App\Rules\MatchOldPasswordRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
  public function __construct()
  {
    $bs = DB::table('basic_settings')
      ->select('facebook_app_id', 'facebook_app_secret')
      ->first();

    Config::set('services.facebook.client_id', $bs->facebook_app_id);
    Config::set('services.facebook.client_secret', $bs->facebook_app_secret);
    Config::set('services.facebook.redirect', url('user/login/facebook/callback'));
  }

  public function login(Request $request)
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_login', 'meta_description_login')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    // get the status of digital product (exist or not in the cart)
    if (!empty($request->input('digital_item'))) {
      $queryResult['digitalProductStatus'] = $request->input('digital_item');
    }

    $queryResult['bs'] = Basic::settings(['google_recaptcha_status', 'facebook_login_status', 'google_login_status']);
    $queryResult['googleLoginEnabled'] = !empty(config('services.google.client_id'))
      && !empty(config('services.google.client_secret'))
      && !empty(config('services.google.redirect'));
    return view('frontend.user.login', $queryResult);
  }

  public function redirectToFacebook()
  {
    return Socialite::driver('facebook')->redirect();
  }

  public function handleFacebookCallback(Request $request)
  {
    if ($request->has('error_code')) {
      Session::flash('error', $request->error_message);
      return redirect()->route('user.login');
    }
    return $this->authenticationViaProvider('facebook');
  }

  public function redirectToGoogle()
  {
    return Socialite::driver('google')->redirect();
  }

  public function handleGoogleCallback()
  {
    try {
      return $this->authenticationViaProvider('google');
    } catch (\Laravel\Socialite\Two\InvalidStateException $exception) {
      Session::flash('error', 'Google sign in expired. Please try again from this browser.');

      return redirect()->route('user.login');
    }
  }

  public function authenticationViaProvider($driver)
  {
    // get the url from session which will be redirect after login
    if (Session::has('redirectTo')) {
      $redirectURL = Session::get('redirectTo');
    } else {
      $redirectURL = route('user.dashboard');
    }

    $responseData = Socialite::driver($driver)->user();
    $userInfo = $responseData->user;
    $email = $responseData->getEmail() ?: ($userInfo['email'] ?? null);

    if (empty($email)) {
      Session::flash('error', 'We could not get an email address from your social account.');

      return redirect()->route('user.login');
    }

    $isUser = User::withTrashed()->where('email', '=', $email)->first();

    if (!empty($isUser)) {
      if (method_exists($isUser, 'trashed') && $isUser->trashed()) {
        $isUser->restore();
      }

      if (Schema::hasColumn('users', 'provider')) {
        $isUser->provider = ($driver == 'facebook') ? 'facebook' : 'google';
      }

      $providerId = $userInfo['id'] ?? $responseData->getId();

      if (Schema::hasColumn('users', 'provider_id') && !empty($providerId)) {
        $isUser->provider_id = $providerId;
      }

      $isUser->email_verified_at = $isUser->email_verified_at ?? Carbon::now();
      $isUser->status = 1;
      $isUser->save();

      Auth::guard('web')->login($isUser);

      return redirect($redirectURL);
    } else {
      // get user avatar and save it
      $avatar = $responseData->getAvatar();
      $fileContents = file_get_contents($avatar);

      $avatarName = $responseData->getId() . '.jpg';
      $path = public_path('assets/img/users/');

      file_put_contents($path . $avatarName, $fileContents);

      // sign up
      $user = new User();

      if ($driver == 'facebook') {
        $user->name = $userInfo['name'] ?? $responseData->getName() ?? Str::before($email, '@');
      } else {
        $user->name = $userInfo['given_name'] ?? $responseData->getName() ?? Str::before($email, '@');
      }

      $user->image = $avatarName;
      $user->username = $this->generateProviderUsername($user->name, $email);
      $user->email = $email;
      $user->status = 1;
      if (Schema::hasColumn('users', 'provider')) {
        $user->provider = ($driver == 'facebook') ? 'facebook' : 'google';
      }
      if (Schema::hasColumn('users', 'provider_id')) {
        $user->provider_id = $userInfo['id'] ?? $responseData->getId();
      }
      $user->address = $userInfo['address'] ?? null;
      $user->save();

      if ($driver === 'google') {
        if ($this->sendVerificationEmail($user)) {
          Session::flash('success', 'A verification mail has been sent to your email address');
        } else {
          Session::flash('warning', 'Your account was created, but the verification email could not be sent. Please contact site admin.');
        }

        return redirect()->route('user.login');
      }

      Auth::guard('web')->login($user);
      return redirect($redirectURL);
    }
  }

  private function generateProviderUsername(?string $name, ?string $email): string
  {
    $base = Str::slug($name ?: Str::before((string) $email, '@'));

    if ($base === '') {
      $base = 'user';
    }

    $username = $base;
    $count = 2;

    while (User::query()->where('username', $username)->exists()) {
      $username = $base . '-' . $count;
      $count++;
    }

    return $username;
  }

  public function loginSubmit(Request $request)
  {
    // get the url from session which will be redirect after login
    if ($request->session()->has('redirectTo')) {
      $redirectURL = $request->session()->get('redirectTo');
    } else {
      $redirectURL = null;
    }

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
      return redirect()->route('user.login')->withErrors($validator->errors())->withInput();
    }

    $login = $request->input('username');
    $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    $credentials = [
      $loginField => $login,
      'password' => $request->password,
    ];

    // login attempt
    if (Auth::guard('web')->attempt($credentials)) {
      $authUser = Auth::guard('web')->user();
      // second, check whether the user's account is active or not
      if ($authUser->email_verified_at == null) {
        Session::flash('error', 'Please verify your email address');

        // logout auth user as condition not satisfied
        Auth::guard('web')->logout();

        return redirect()->back();
      }
      if ($authUser->status == 0) {
        Session::flash('error', 'Sorry, your account has been deactivated');

        // logout auth user as condition not satisfied
        Auth::guard('web')->logout();

        return redirect()->back();
      }

      // otherwise, redirect auth user to next url
      if (is_null($redirectURL)) {
        return redirect()->route('user.dashboard');
      } else {
        // before, redirect to next url forget the session value
        $request->session()->forget('redirectTo');

        return redirect($redirectURL);
      }
    } else {
      Session::flash('error', 'Incorrect username or password');

      return redirect()->back()->withInput();
    }
  }

  public function forgetPassword()
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_forget_password', 'meta_description_forget_password')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();
    $queryResult['bs'] = Basic::settings(['google_recaptcha_status', 'facebook_login_status', 'google_login_status']);

    return view('frontend.user.forget-password', $queryResult);
  }

  public function forgetPasswordMail(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email:rfc,dns',
        new MatchEmailRule('user')
      ]
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
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    $user = User::query()->where('email', '=', $request->email)->first();

    // store user email in session to use it later
    $request->session()->put('userEmail', $user->email);

    // get the mail template information from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'reset_password')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // get the website title info from db
    $info = Basic::select('website_title')->first();

    $name = $user->username;

    $link = '<a href=' . url("user/reset-password") . '>Click Here</a>';

    $mailBody = str_replace('{customer_name}', $name, $mailBody);
    $mailBody = str_replace('{password_reset_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $user->email;

    $mailData['sessionMessage'] = 'A mail has been sent to your email address';

    BasicMailer::sendMail($mailData);

    return redirect()->back();
  }

  public function resetPassword()
  {
    $misc = new MiscellaneousController();

    $bgImg = $misc->getBreadcrumb();

    return view('frontend.user.reset-password', compact('bgImg'));
  }

  public function resetPasswordSubmit(Request $request)
  {
    if ($request->session()->has('userEmail')) {
      // get the user email from session
      $emailAddress = $request->session()->get('userEmail');

      $rules = [
        'new_password' => 'required|confirmed',
        'new_password_confirmation' => 'required'
      ];

      $messages = [
        'new_password.confirmed' => 'Password confirmation failed.',
        'new_password_confirmation.required' => 'The confirm new password field is required.'
      ];

      $validator = Validator::make($request->all(), $rules, $messages);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator->errors());
      }

      $user = User::query()->where('email', '=', $emailAddress)->first();

      $user->update([
        'password' => Hash::make($request->new_password)
      ]);

      Session::flash('success', 'Password updated successfully.');
    } else {
      Session::flash('error', 'Something went wrong!');
    }

    return redirect()->route('user.login');
  }

  public function signup()
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_signup', 'meta_description_signup')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $queryResult['bs'] = Basic::settings(['google_recaptcha_status', 'facebook_login_status', 'google_login_status']);
    $queryResult['googleLoginEnabled'] = !empty(config('services.google.client_id'))
      && !empty(config('services.google.client_secret'))
      && !empty(config('services.google.redirect'));

    $queryResult['recaptchaInfo'] = Basic::settings(['google_recaptcha_status']);

    return view('frontend.user.signup', $queryResult);
  }

  public function signupSubmit(Request $request)
  {
    $info = Basic::settings(['google_recaptcha_status']);

    // validation start
    $rules = [
      'username' => ['required', 'max:255', Rule::unique('users', 'username')->whereNull('deleted_at')],
      'email' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
      'password' => 'required|confirmed',
      'password_confirmation' => 'required'
    ];

    if (Basic::recaptchaEnabled()) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [
      'password_confirmation.required' => 'The confirm password field is required.'
    ];

    if (Basic::recaptchaEnabled()) {
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }
    // validation end

    $user = new User();
    $user->username = $request->username;
    $user->email = $request->email;
    $user->status = 1;
    $user->password = Hash::make($request->password);
    $user->save();

    $this->sendVerificationEmail($user);

    $queryResult['authUser'] = $user;
    return back()->with('success', 'A verification mail has been sent to your email address');
  }


  private function sendVerificationEmail(User $user): bool
  {
    $info = Basic::settings(['website_title']);

    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'verify_email')->first();

    if (!$mailTemplate) {
      Session::flash('warning', 'Verification email template is missing.');
      return false;
    }

    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    $link = '<a href=' . url("user/signup-verify/" . $user->id) . '>Click Here</a>';

    $mailBody = str_replace('{username}', $user->username, $mailBody);
    $mailBody = str_replace('{verification_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $user->email;

    $mailData['sessionMessage'] = 'A verification mail has been sent to your email address';

    return BasicMailer::sendMail($mailData);
  }

  public function signupVerify($id)
  {
    $user = User::where('id', $id)->firstOrFail();
    $user->email_verified_at = Carbon::now();
    $user->save();
    Auth::login($user);
    return redirect()->route('user.dashboard');
  }

  public function redirectToDashboard()
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['language'] = $language;

    $queryResult['bgImg'] = $misc->getBreadcrumb();
    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $user = Auth::guard('web')->user();

    $queryResult['authUser'] = $user;
    $queryResult['wishlists'] = Wishlist::where('user_id', $user->id)
      ->count();
    $queryResult['appointments'] = ServiceBooking::where('user_id', $user->id)
      ->count();
    $queryResult['orders'] = ProductOrder::where('user_id', $user->id)
      ->count();


    return view('frontend.user.dashboard', $queryResult);
  }

  public function editProfile()
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();
    $language = $misc->getLanguage();
    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['authUser'] = Auth::guard('web')->user();

    return view('frontend.user.edit-profile', $queryResult);
  }

  public function updateProfile(Request $request)
  {

    $data = $request->validate([
      'name' => 'required|string|max:255',
      'username' => [
        'required',
        'alpha_dash',
        Rule::unique('users', 'username')->ignore(Auth::guard('web')->user()->id)->whereNull('deleted_at'),
      ],
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore(Auth::guard('web')->user()->id)->whereNull('deleted_at')
      ],
      'phone' => 'nullable|string|max:50',
      'country' => 'nullable|string|max:255',
      'city' => 'nullable|string|max:255',
      'state' => 'nullable|string|max:255',
      'zip_code' => 'nullable|string|max:50',
      'address' => 'nullable|string|max:500',
      'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
    ]);

    $authUser = Auth::guard('web')->user();
    $in = collect($data)->except('image')->toArray();
    $file = $request->file('image');
    if ($file) {
      $extension = $file->getClientOriginalExtension();
      $directory = public_path('assets/img/users/');
      $fileName = uniqid() . '.' . $extension;
      @mkdir($directory, 0775, true);
      $file->move($directory, $fileName);
      $in['image'] = $fileName;
    }

    $authUser->update($in);

    Session::flash('success', 'Your profile has been updated successfully.');

    return redirect()->back();
  }

  public function changePassword()
  {
    $misc = new MiscellaneousController();

    $bgImg = $misc->getBreadcrumb();
    $language = $misc->getLanguage();
    $pageHeading = $misc->getPageHeading($language);

    return view('frontend.user.change-password', compact('bgImg', 'pageHeading'));
  }

  public function updatePassword(Request $request)
  {
    $rules = [
      'current_password' => [
        'required',
        new MatchOldPasswordRule('user')
      ],
      'new_password' => 'required|confirmed',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation failed.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    $user = Auth::guard('web')->user();

    $user->update([
      'password' => Hash::make($request->new_password)
    ]);

    Session::flash('success', 'Password updated successfully.');

    return redirect()->back();
  }

  //wishlist
  public function wishlist(Request $request)
  {
    $misc = new MiscellaneousController();
    $bgImg = $misc->getBreadcrumb();
    $language = $misc->getLanguage();
    $information['language'] = $language;
    $information['bgImg'] = $bgImg;
    $information['pageHeading'] = $misc->getPageHeading($language);

    $user_id = Auth::guard('web')->user()->id;
    $wishlists = Wishlist::where('user_id', $user_id)
      ->join('services', 'services.id', '=', 'wishlists.service_id')
      ->join('service_contents', 'wishlists.service_id', '=', 'service_contents.service_id')
      ->where('service_contents.language_id', $language->id)
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
      ->when('services.vendor_id' != "0", function ($query) {
        return $query->leftJoin('vendors', 'services.vendor_id', '=', 'vendors.id')
          ->where(function ($query) {
            $query->where([
              ['vendors.status', '=', 1],
            ])->orWhere('services.vendor_id', '=', 0);
          });
      })
      ->when($request->service, function ($query) use ($request) {
        $query->where(function ($subQuery) use ($request) {
          $subQuery->where('service_contents.name', 'like', '%' . $request->service . '%');
        });
      })
      ->select(
        'wishlists.*',
        'wishlists.service_id',
        'services.service_image',
        'services.average_rating',
        'services.price',
        'service_contents.name',
        'service_contents.slug',
      )
      ->paginate(10);

    $information['wishlists'] = $wishlists;
    return view('frontend.user.wishlist', $information);
  }
  //add_to_wishlist
  public function add_to_wishlist($id)
  {
    if (Auth::guard('web')->check()) {
      $user_id = Auth::guard('web')->user()->id;
      $check = Wishlist::where('service_id', $id)->where('user_id', $user_id)->first();

      if (!empty($check)) {
        $notification = array('message' => 'You already added this event into your wishlist..!', 'alert-type' => 'error');
        return back()->with($notification);
      } else {
        $service =  Services::where('id', $id)->select('vendor_id')->firstOrFail();
        $add = new Wishlist;
        $add->service_id = $id;
        $add->user_id = $user_id;
        $add->vendor_id = $service->vendor_id;
        $add->save();
        $notification = array('message' => 'Added to your wishlist successfully.', 'alert-type' => 'success');
        return back()->with($notification);
      }
    } else {
      return redirect()->route('user.login');
    }
  }

  //remove_wishlist
  public function remove_wishlist($id)
  {
    if (Auth::guard('web')->check()) {
      $remove = Wishlist::where('service_id', $id)->first();
      if ($remove) {
        $remove->delete();
        $notification = array('message' => 'Removed From wishlist successfully..!', 'alert-type' => 'info');
      } else {
        $notification = array('message' => 'Something went wrong', 'alert-type' => 'danger');
      }
      return back()->with($notification);
    } else {
      return redirect()->route('user.login');
    }
  }


  public function logoutSubmit(Request $request)
  {
    Auth::guard('web')->logout();
    Session::forget('secret_login');
    Session::forget('discount');

    if ($request->session()->has('redirectTo')) {
      $request->session()->forget('redirectTo');
    }

    return redirect()->route('user.login');
  }
}
