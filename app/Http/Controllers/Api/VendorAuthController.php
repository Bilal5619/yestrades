<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Services\ServiceCategory;
use App\Services\VendorAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class VendorAuthController extends Controller
{
  private VendorAuthService $vendorAuthService;

  public function __construct(VendorAuthService $vendorAuthService)
  {
    $this->vendorAuthService = $vendorAuthService;
  }

  public function signup(Request $request): JsonResponse
  {
    try {
      $result = $this->vendorAuthService->registerVendor($request->all());
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed.',
        'errors' => $e->errors(),
      ], 422);
    } catch (Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Vendor signup could not be completed.',
      ], 500);
    }

    return response()->json([
      'success' => true,
      'message' => $this->signupMessage($result),
      'data' => [
        'vendor' => $this->vendorPayload($result['vendor']->loadMissing('serviceCategories')),
        'requires_email_verification' => $result['requires_email_verification'],
        'requires_admin_approval' => $result['requires_admin_approval'],
      ],
    ], 201);
  }

  public function login(Request $request): JsonResponse
  {
    try {
      $vendor = $this->vendorAuthService->authenticateVendor($request->all());
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => collect($e->errors())->flatten()->first() ?? 'Authentication failed.',
        'errors' => $e->errors(),
      ], 422);
    }

    $token = $vendor->createToken('vendor-mobile')->plainTextToken;
    $setting = $this->vendorAuthService->vendorSetting();

    return response()->json([
      'success' => true,
      'message' => 'Login successful.',
      'data' => [
        'token_type' => 'Bearer',
        'access_token' => $token,
        'vendor' => $this->vendorPayload($vendor->fresh(['vendor_info', 'serviceCategories'])),
        'requires_admin_approval' => (int) $setting->vendor_admin_approval === 1 && (int) $vendor->status === 0,
      ],
    ]);
  }

  public function serviceCategories(Request $request): JsonResponse
  {
    $language = $request->filled('language')
      ? Language::where('code', $request->input('language'))->first()
      : Language::where('is_default', 1)->first();
    $language = $language ?: Language::where('is_default', 1)->first();

    $categories = ServiceCategory::query()
      ->when($language, fn ($query) => $query->where('language_id', $language->id))
      ->where('status', 1)
      ->where('name', '!=', 'Landlord Certificates')
      ->orderBy('serial_number')
      ->orderBy('name')
      ->get(['id', 'name']);

    return response()->json([
      'success' => true,
      'data' => [
        'service_categories' => $categories,
      ],
    ]);
  }

  private function signupMessage(array $result): string
  {
    if ($result['requires_email_verification']) {
      return 'A verification mail has been sent to your email address.';
    }

    if ($result['requires_admin_approval']) {
      return 'Sign up completed. Your account is pending admin approval.';
    }

    return 'Sign up successfully completed. Please login now.';
  }

  private function vendorPayload($vendor): array
  {
    return [
      'id' => $vendor->id,
      'username' => $vendor->username,
      'email' => $vendor->email,
      'phone' => $vendor->phone,
      'status' => $vendor->status,
      'business_type' => $vendor->business_type,
      'email_verified_at' => optional($vendor->email_verified_at)->toDateTimeString(),
      'service_categories' => $vendor->relationLoaded('serviceCategories')
        ? $vendor->serviceCategories->map(fn ($category) => [
          'id' => $category->id,
          'name' => $category->name,
        ])->values()
        : [],
      'profile' => $vendor->vendor_info ? [
        'name' => $vendor->vendor_info->name,
        'country' => $vendor->vendor_info->country,
        'city' => $vendor->vendor_info->city,
        'state' => $vendor->vendor_info->state,
        'zip_code' => $vendor->vendor_info->zip_code,
        'postcode' => $vendor->vendor_info->postcode ?: $vendor->vendor_info->zip_code,
        'address' => $vendor->vendor_info->address,
        'details' => $vendor->vendor_info->details,
      ] : null,
    ];
  }
}
