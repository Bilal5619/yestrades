<?php

namespace App\Services;

use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffContent;
use App\Models\Staff\StaffGlobalDay;
use App\Models\Vendor;
use App\Models\VendorInfo;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VendorAuthService
{
  public function validateSignup(array $data, bool $includeRecaptcha = false): array
  {
    $data['postcode'] = $data['postcode'] ?? $data['zip_code'] ?? null;
    $data['address'] = $data['address'] ?? $data['selected_address'] ?? $data['full_address'] ?? null;

    $rules = [
      'username' => 'required|unique:vendors',
      'name' => 'required|max:255',
      'email' => 'required|email|unique:vendors',
      'phone' => 'required|max:255',
      'country' => 'nullable|max:255',
      'city' => 'nullable|max:255',
      'state' => 'nullable|max:255',
      'postcode' => 'required|max:255',
      'zip_code' => 'nullable|max:255',
      'address' => 'required|max:1000',
      'business_type' => 'required|in:sole_trader,limited_company',
      'service_category_ids' => 'required|array|min:1',
      'service_category_ids.*' => 'integer|exists:service_categories,id',
      'password' => 'required|confirmed|min:10',
    ];

    $messages = [];

    if ($includeRecaptcha && Basic::recaptchaEnabled()) {
      $rules['g-recaptcha-response'] = 'required|captcha';
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validated = Validator::make($data, $rules, $messages)->validate();

    if (($validated['username'] ?? null) === 'admin') {
      throw ValidationException::withMessages([
        'username' => ['You can not use admin as a username!'],
      ]);
    }

    return $validated;
  }

  public function registerVendor(array $data, bool $includeRecaptcha = false): array
  {
    $validated = $this->validateSignup($data, $includeRecaptcha);
    $setting = $this->vendorSetting();
    $verificationEmailSent = false;

    if ((int) $setting->vendor_email_verification === 1) {
      $verificationEmailSent = $this->sendVerificationEmail($validated['email'], $validated['username']);
    }

    return DB::transaction(function () use ($validated, $data, $setting, $verificationEmailSent) {
      $vendorData = array_merge($data, $validated);
      $vendorData['password'] = Hash::make($validated['password']);
      $vendorData['recived_email'] = $validated['email'];
      $vendorData['country'] = null;
      $vendorData['city'] = null;
      $vendorData['state'] = null;
      $vendorData['zip_code'] = $validated['postcode'];

      if ((int) $setting->vendor_email_verification === 0 || !$verificationEmailSent) {
        $vendorData['email_verified_at'] = now();
      }

      if ((int) $setting->vendor_admin_approval === 0 && (int) $setting->vendor_email_verification === 0) {
        $vendorData['status'] = 1;
      } else {
        $vendorData['status'] = 0;
      }

      $vendor = Vendor::create($vendorData);
      $vendor->serviceCategories()->sync($validated['service_category_ids']);

      $language = Language::where('is_default', 1)->first() ?? Language::query()->first();

      VendorInfo::create([
        'language_id' => optional($language)->id,
        'vendor_id' => $vendor->id,
        'name' => $validated['name'],
        'country' => null,
        'city' => null,
        'state' => null,
        'zip_code' => $validated['postcode'],
        'postcode' => $validated['postcode'],
        'address' => $validated['address'],
        'details' => $data['details'] ?? null,
      ]);

      $this->createDefaultGlobalDays($vendor);
      $this->createVendorStaff($vendor, $validated, $data);

      return [
        'vendor' => $vendor->fresh('vendor_info'),
        'requires_email_verification' => $verificationEmailSent,
        'requires_admin_approval' => (int) $setting->vendor_admin_approval === 1,
      ];
    });
  }

  public function validateLogin(array $data, bool $includeRecaptcha = false): array
  {
    $rules = [
      'email' => 'required|email',
      'password' => 'required',
    ];

    $messages = [];

    if ($includeRecaptcha && Basic::recaptchaEnabled()) {
      $rules['g-recaptcha-response'] = 'required|captcha';
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    return Validator::make($data, $rules, $messages)->validate();
  }

  public function authenticateVendor(array $data, bool $includeRecaptcha = false): Vendor
  {
    $validated = $this->validateLogin($data, $includeRecaptcha);

    $vendor = Vendor::where('email', $validated['email'])->first();

    if (!$vendor || !Hash::check($validated['password'], $vendor->password)) {
      throw ValidationException::withMessages([
        'email' => ['Incorrect email or password'],
      ]);
    }

    $setting = $this->vendorSetting();

    if (
      (int) $setting->vendor_email_verification === 1
      && $vendor->email_verified_at === null
      && (int) $vendor->status === 0
    ) {
      throw ValidationException::withMessages([
        'email' => ['Please verify your email address'],
      ]);
    }

    return $vendor;
  }

  public function vendorSetting(): object
  {
    return DB::table('basic_settings')
      ->where('uniqid', 12345)
      ->select('vendor_email_verification', 'vendor_admin_approval')
      ->first() ?? (object) [
        'vendor_email_verification' => 0,
        'vendor_admin_approval' => 0,
      ];
  }

  private function sendVerificationEmail(string $email, string $username): bool
  {
    $mailTemplate = MailTemplate::where('mail_type', 'verify_email')->first();
    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    if (!$mailTemplate || !$info || (int) $info->smtp_status !== 1) {
      return false;
    }

    $link = '<a href="' . url('vendor/email/verify?token=' . rawurlencode($email)) . '">Click Here</a>';
    $mailBody = str_replace('{username}', $username, $mailTemplate->mail_body);
    $mailBody = str_replace('{verification_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

    Config::set('mail.mailers.smtp', [
      'transport' => 'smtp',
      'host' => $info->smtp_host,
      'port' => $info->smtp_port,
      'encryption' => $info->encryption,
      'username' => $info->smtp_username,
      'password' => $info->smtp_password,
      'timeout' => null,
      'auth_mode' => null,
    ]);

    Mail::send([], [], function (Message $message) use ($email, $mailTemplate, $mailBody, $info) {
      $message->to($email)
        ->subject($mailTemplate->mail_subject)
        ->from($info->from_mail, $info->from_name)
        ->html($mailBody, 'text/html');
    });

    return true;
  }

  private function createDefaultGlobalDays(Vendor $vendor): void
  {
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    foreach ($days as $key => $day) {
      StaffGlobalDay::create([
        'day' => $day,
        'vendor_id' => $vendor->id,
        'indx' => $key,
      ]);
    }
  }

  private function createVendorStaff(Vendor $vendor, array $validated, array $data): void
  {
    $staff = Staff::create([
      'username' => $validated['username'],
      'password' => Hash::make($validated['password']),
      'vendor_id' => $vendor->id,
      'email' => $validated['email'],
      'status' => 1,
      'order_number' => 0,
      'allow_login' => 1,
      'role' => 'vendor',
    ]);

    foreach (Language::all() as $language) {
      StaffContent::create([
        'language_id' => $language->id,
        'staff_id' => $staff->id,
        'name' => $validated['name'],
        'location' => trim($validated['address']),
        'information' => $data['details'] ?? null,
      ]);
    }
  }
}
