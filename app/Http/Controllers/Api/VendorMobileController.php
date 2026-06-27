<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppointmentCompletion;
use App\Http\Helpers\UploadFile;
use App\Models\Admin\Transaction;
use App\Models\BasicSettings\Basic;
use App\Models\Language;
use App\Models\Services\AppointmentCompletionImage;
use App\Models\Services\Invoice;
use App\Models\Services\ServiceBooking;
use App\Models\Services\Services;
use App\Models\Staff\Staff;
use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Models\VendorInfo;
use App\Models\VendorRegistrationNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class VendorMobileController extends Controller
{
  private array $requiredDocTypes = [
    VendorDocument::TYPE_INSURANCE,
    VendorDocument::TYPE_ID,
    VendorDocument::TYPE_RIGHT_TO_WORK,
  ];

  public function me(Request $request): JsonResponse
  {
    return $this->success([
      'vendor' => $this->vendorPayload($this->vendor($request)->loadMissing('vendor_info', 'serviceCategories', 'documents', 'registrationNumbers')),
    ]);
  }

  public function logout(Request $request): JsonResponse
  {
    if ($request->user()->currentAccessToken()) {
      $request->user()->currentAccessToken()->delete();
    }

    return $this->success(null, 'Logged out successfully.');
  }

  public function dashboard(Request $request): JsonResponse
  {
    $vendor = $this->vendor($request);
    $vendorId = $vendor->id;
    $languageId = optional(Language::where('is_default', 1)->first())->id;

    $recentAppointments = $this->appointmentQuery($vendorId, $languageId)
      ->orderByDesc('id')
      ->take(8)
      ->get()
      ->map(fn ($booking) => $this->appointmentPayload($booking));

    $totalEarned = Transaction::query()
      ->where('vendor_id', $vendorId)
      ->whereIn('transaction_type', ['service_booking', 'booking_refund'])
      ->where('payment_status', 'completed')
      ->sum(DB::raw('COALESCE(after_balance, 0) - COALESCE(pre_balance, 0)'));

    return $this->success([
      'summary' => [
        'total_balance' => $vendor->amount,
        'total_earned' => $totalEarned,
        'total_services' => Services::where('vendor_id', $vendorId)->count(),
        'total_appointments' => ServiceBooking::where('vendor_id', $vendorId)->count(),
        'pending_appointments' => ServiceBooking::where('vendor_id', $vendorId)->where('order_status', 'pending')->count(),
        'completed_appointments' => ServiceBooking::where('vendor_id', $vendorId)->where('order_status', 'completed')->count(),
        'rejected_appointments' => ServiceBooking::where('vendor_id', $vendorId)->where('order_status', 'rejected')->count(),
      ],
      'recent_appointments' => $recentAppointments,
    ]);
  }

  public function updateProfile(Request $request): JsonResponse
  {
    $vendor = $this->vendor($request);

    $validated = $request->validate([
      'name' => ['nullable', 'string', 'max:255'],
      'shop_name' => ['nullable', 'string', 'max:255'],
      'phone' => ['nullable', 'string', 'max:255'],
      'country' => ['nullable', 'string', 'max:255'],
      'city' => ['nullable', 'string', 'max:255'],
      'state' => ['nullable', 'string', 'max:255'],
      'zip_code' => ['nullable', 'string', 'max:255'],
      'postcode' => ['nullable', 'string', 'max:255'],
      'address' => ['nullable', 'string', 'max:500'],
      'details' => ['nullable', 'string'],
    ]);

    if (array_key_exists('phone', $validated)) {
      $vendor->update(['phone' => $validated['phone']]);
    }

    $languageId = optional(Language::where('is_default', 1)->first())->id;
    VendorInfo::updateOrCreate(
      ['vendor_id' => $vendor->id],
      array_merge(
        ['language_id' => $languageId],
        collect($validated)->except('phone')->toArray()
      )
    );

    return $this->success([
      'vendor' => $this->vendor($request)->fresh(['vendor_info', 'serviceCategories']),
    ], 'Profile updated successfully.');
  }

  public function changePassword(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'current_password' => ['required', 'string'],
      'new_password' => ['required', 'string', 'min:6', 'confirmed'],
    ]);

    $vendor = $this->vendor($request);
    if (!Hash::check($validated['current_password'], $vendor->password)) {
      return $this->error('The current password is incorrect.', 422, [
        'current_password' => ['The current password is incorrect.'],
      ]);
    }

    $vendor->update(['password' => Hash::make($validated['new_password'])]);

    return $this->success(null, 'Password updated successfully.');
  }

  public function accountSetup(Request $request): JsonResponse
  {
    $vendor = $this->vendor($request)->loadMissing('vendor_info', 'serviceCategories', 'documents', 'registrationNumbers');

    return $this->success([
      'vendor' => $this->vendorPayload($vendor),
      'registration_numbers' => $vendor->registrationNumbers->values(),
      'documents' => $vendor->documents->map(fn ($document) => $this->documentPayload($document))->values(),
      'tasks' => $this->buildTasks($vendor),
      'can_submit' => $this->canSubmitVerification($vendor),
    ]);
  }

  public function updateBusinessType(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'business_type' => ['required', 'string', 'max:255'],
    ]);

    $this->vendor($request)->update($validated);

    return $this->success(null, 'Business type updated successfully.');
  }

  public function storeRegistrationNumber(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'category' => ['nullable', 'string', 'max:255'],
      'title' => ['nullable', 'string', 'max:255'],
      'number' => ['nullable', 'string', 'max:255'],
      'registration_number' => ['nullable', 'string', 'max:255'],
      'registration_authority' => ['nullable', 'string', 'max:255'],
      'expiry_date' => ['nullable', 'date'],
    ]);

    $validated['vendor_id'] = $this->vendor($request)->id;
    $validated['category'] = $validated['category'] ?? 'registration';
    $validated['registration_number'] = $validated['registration_number'] ?? ($validated['number'] ?? null);

    $registration = VendorRegistrationNumber::create($validated);

    return $this->success(['registration_number' => $registration], 'Registration number added successfully.', 201);
  }

  public function deleteRegistrationNumber(Request $request, $id): JsonResponse
  {
    VendorRegistrationNumber::where('vendor_id', $this->vendor($request)->id)->findOrFail($id)->delete();

    return $this->success(null, 'Registration number deleted successfully.');
  }

  public function storeDocument(Request $request): JsonResponse
  {
    $vendor = $this->vendor($request);
    $type = $request->input('type');

    if (!in_array($type, $this->requiredDocTypes, true)) {
      return $this->error('Unknown document type.', 422, ['type' => ['Unknown document type.']]);
    }

    if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && $request->boolean('right_to_work_is_british')) {
      $vendor->update(['right_to_work_is_british' => true]);

      return $this->success(null, 'Right to Work marked as not required.');
    }

    $existing = VendorDocument::where('vendor_id', $vendor->id)->where('type', $type)->first();

    $rules = [
      'type' => ['required', Rule::in($this->requiredDocTypes)],
      'file' => [(!$existing || $existing->is_rejected || $existing->is_approved) ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
      'policy_number' => ['nullable', 'string', 'max:255'],
      'id_type' => ['nullable', Rule::in(['driving_licence', 'passport'])],
      'expiry_date' => ['nullable', 'date'],
      'date_of_birth' => ['nullable', 'date', 'before:today'],
    ];

    if ($type === VendorDocument::TYPE_INSURANCE || $type === VendorDocument::TYPE_ID) {
      $rules['expiry_date'] = ['required', 'date'];
    }
    if ($type === VendorDocument::TYPE_ID) {
      $rules['id_type'] = ['required', Rule::in(['driving_licence', 'passport'])];
    }
    if ($type === VendorDocument::TYPE_RIGHT_TO_WORK) {
      $rules['expiry_date'] = ['required', 'date'];
      $rules['date_of_birth'] = ['required', 'date', 'before:today'];
      $vendor->update(['right_to_work_is_british' => false]);
    }

    $validated = $request->validate($rules);
    $attributes = [
      'vendor_id' => $vendor->id,
      'type' => $type,
      'status' => VendorDocument::STATUS_UNDER_REVIEW,
      'rejection_reason' => null,
      'policy_number' => $validated['policy_number'] ?? null,
      'id_type' => $validated['id_type'] ?? null,
      'expiry_date' => $validated['expiry_date'] ?? null,
      'date_of_birth' => $validated['date_of_birth'] ?? null,
    ];

    if ($request->hasFile('file')) {
      $attributes['file_path'] = UploadFile::store(public_path(VendorDocument::STORAGE_DIR), $request->file('file'));
      if ($existing && $existing->file_path) {
        @unlink(public_path(VendorDocument::STORAGE_DIR . $existing->file_path));
      }
    } elseif ($existing) {
      $attributes['file_path'] = $existing->file_path;
    }

    $document = VendorDocument::updateOrCreate(
      ['vendor_id' => $vendor->id, 'type' => $type],
      $attributes
    );

    return $this->success(['document' => $this->documentPayload($document)], 'Document uploaded successfully.');
  }

  public function submitVerification(Request $request): JsonResponse
  {
    $vendor = $this->vendor($request)->loadMissing('vendor_info', 'serviceCategories', 'documents');

    if (!$this->canSubmitVerification($vendor)) {
      return $this->error('Please complete all required setup tasks before submitting.', 422);
    }

    if (in_array($vendor->verification_status, ['submitted', 'approved'], true)) {
      return $this->error('Your verification has already been submitted.', 422);
    }

    $vendor->update([
      'verification_status' => 'submitted',
      'verification_rejection_reason' => null,
    ]);

    return $this->success(null, 'Verification submitted. Approval can take up to 24 hours.');
  }

  public function appointments(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'order_status' => ['nullable', 'string'],
      'payment_status' => ['nullable', 'string'],
      'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    $languageId = optional(Language::where('is_default', 1)->first())->id;
    $appointments = $this->appointmentQuery($this->vendor($request)->id, $languageId)
      ->when($validated['order_status'] ?? null, fn ($query, $status) => $query->where('order_status', $status))
      ->when($validated['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
      ->orderByDesc('id')
      ->paginate($validated['per_page'] ?? 15);

    return $this->success([
      'appointments' => $appointments->through(fn ($booking) => $this->appointmentPayload($booking)),
    ]);
  }

  public function appointment(Request $request, $id): JsonResponse
  {
    $booking = $this->appointmentForVendor($request, $id);

    return $this->success(['appointment' => $this->appointmentPayload($booking)]);
  }

  public function updateAppointmentStatus(Request $request, $id): JsonResponse
  {
    $validated = $request->validate([
      'order_status' => ['required', Rule::in(['pending', 'accepted', 'rejected'])],
    ]);

    $booking = $this->appointmentForVendor($request, $id);

    if ($booking->order_status === 'completed') {
      return $this->error('Completed appointments cannot be changed.', 422);
    }
    if ($booking->order_status !== 'pending' && $booking->order_status !== $validated['order_status']) {
      return $this->error('Only pending appointments can be accepted or rejected.', 422);
    }

    DB::transaction(function () use ($booking, $validated) {
      $locked = ServiceBooking::where('vendor_id', $booking->vendor_id)->lockForUpdate()->findOrFail($booking->id);
      $locked->update([
        'order_status' => $validated['order_status'],
        'refund' => $validated['order_status'] === 'rejected' ? 'pending' : $locked->refund,
      ]);

      if ($validated['order_status'] === 'accepted') {
        Vendor::where('id', $locked->vendor_id)->decrement('total_appointment');
      } elseif ($validated['order_status'] === 'rejected') {
        Vendor::where('id', $locked->vendor_id)->increment('total_appointment');
      }
    });

    return $this->success(['appointment' => $this->appointmentPayload($booking->fresh())], 'Appointment status updated successfully.');
  }

  public function completeAppointment(Request $request, $id): JsonResponse
  {
    $validated = $request->validate([
      'completion_images' => ['required', 'array', 'min:1', 'max:10'],
      'completion_images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
      'job_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
    ]);

    $vendor = $this->vendor($request);
    $booking = $this->appointmentForVendor($request, $id);
    if ($booking->order_status !== 'accepted') {
      return $this->error('Only accepted appointments can be completed.', 422);
    }

    $storedFiles = [];
    try {
      DB::transaction(function () use ($request, $booking, $vendor, &$storedFiles) {
        $locked = ServiceBooking::where('vendor_id', $vendor->id)->lockForUpdate()->findOrFail($booking->id);
        if ($locked->order_status !== 'accepted') {
          throw new \LogicException('Only accepted appointments can be completed.');
        }

        foreach ($request->file('completion_images') as $image) {
          $fileName = UploadFile::store(public_path('assets/img/appointment-completions/'), $image);
          $storedFiles[] = public_path('assets/img/appointment-completions/' . $fileName);
          AppointmentCompletionImage::create([
            'service_booking_id' => $locked->id,
            'image' => $fileName,
            'uploaded_by_type' => 'vendor',
            'uploaded_by_id' => $vendor->id,
          ]);
        }

        $certificate = UploadFile::store(public_path('assets/file/certificates/'), $request->file('job_certificate'));
        $storedFiles[] = public_path('assets/file/certificates/' . $certificate);
        AppointmentCompletion::complete($locked, 'vendor', $vendor->id);
        $locked->update(['job_certificate' => $certificate]);
      });
    } catch (\Throwable $exception) {
      foreach ($storedFiles as $file) {
        @unlink($file);
      }
      report($exception);
      return $this->error('Appointment completion failed. Please try again.', 500);
    }

    return $this->success(['appointment' => $this->appointmentPayload($booking->fresh(['completionImages']))], 'Appointment completed successfully.');
  }

  public function storeInvoice(Request $request, $id): JsonResponse
  {
    $booking = $this->appointmentForVendor($request, $id);
    if ($booking->order_status !== 'completed') {
      return $this->error('You can only upload an invoice for a completed job.', 422);
    }
    if ($booking->vendorInvoice) {
      return $this->error('An invoice has already been submitted for this job.', 422);
    }

    $validated = $request->validate([
      'invoice_date' => ['required', 'date'],
      'amount' => ['required', 'numeric', 'min:0'],
      'vat' => ['nullable', 'numeric', 'min:0'],
      'invoice_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
      'bank_details' => ['nullable', 'string', 'max:2000'],
      'notes' => ['nullable', 'string', 'max:2000'],
    ]);

    $fileName = $request->hasFile('invoice_file')
      ? UploadFile::store(public_path('assets/file/invoices/vendor/'), $request->file('invoice_file'))
      : null;
    $amount = (float) $validated['amount'];
    $vat = isset($validated['vat']) ? (float) $validated['vat'] : 0;
    $commissionPercentage = 20;
    $commissionAmount = round(($amount * $commissionPercentage) / 100, 2);

    $invoice = DB::transaction(function () use ($request, $booking, $validated, $fileName, $amount, $vat, $commissionPercentage, $commissionAmount) {
      return Invoice::create([
        'service_booking_id' => $booking->id,
        'vendor_id' => $this->vendor($request)->id,
        'invoice_number' => $this->generateInvoiceNumber(),
        'invoice_date' => $validated['invoice_date'],
        'service_title' => optional($booking->serviceContent->first())->name,
        'vendor_name' => optional($this->vendor($request)->vendor_info)->name ?: $this->vendor($request)->username,
        'amount' => $amount,
        'vat' => $vat ?: null,
        'total_amount' => $amount + $vat,
        'customer_paid_amount' => $booking->customer_paid,
        'commission_percentage' => $commissionPercentage,
        'commission_amount' => $commissionAmount,
        'vendor_payout_amount' => round($amount - $commissionAmount, 2),
        'invoice_file' => $fileName,
        'bank_details' => $validated['bank_details'] ?? null,
        'notes' => $validated['notes'] ?? null,
        'invoice_status' => 'submitted',
        'payment_status' => 'unpaid',
      ]);
    });

    return $this->success(['invoice' => $invoice], 'Invoice submitted successfully.', 201);
  }

  public function staff(Request $request): JsonResponse
  {
    $staff = Staff::with('StaffContent')
      ->where('vendor_id', $this->vendor($request)->id)
      ->orderBy('order_number')
      ->orderBy('id')
      ->get();

    return $this->success(['staff' => $staff]);
  }

  public function assignStaff(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'appointment_id' => ['required', 'integer'],
      'staff_id' => ['required', 'integer'],
    ]);

    $vendor = $this->vendor($request);
    $staff = Staff::where('vendor_id', $vendor->id)->findOrFail($validated['staff_id']);
    $booking = ServiceBooking::where('vendor_id', $vendor->id)->findOrFail($validated['appointment_id']);
    $booking->update(['staff_id' => $staff->id]);

    return $this->success(['appointment' => $this->appointmentPayload($booking->fresh('staff'))], 'New staff assigned successfully.');
  }

  public function availableJobs(Request $request): JsonResponse
  {
    $vendor = $this->vendor($request)->loadMissing('serviceCategories');
    $categoryIds = $vendor->serviceCategories->pluck('id')->all();
    $languageId = optional(Language::where('is_default', 1)->first())->id;

    if (empty($categoryIds)) {
      return $this->success([
        'needs_categories' => true,
        'jobs' => [],
      ]);
    }

    $jobs = ServiceBooking::with(['service', 'serviceContent' => function ($query) use ($languageId) {
      $query->when($languageId, fn ($q) => $q->where('language_id', $languageId));
    }])
      ->where('vendor_id', 0)
      ->where('order_status', 'pending')
      ->whereHas('serviceContent', function ($query) use ($categoryIds, $languageId) {
        $query->when($languageId, fn ($q) => $q->where('language_id', $languageId))
          ->whereIn('category_id', $categoryIds);
      })
      ->orderByDesc('id')
      ->get()
      ->map(fn ($booking) => $this->appointmentPayload($booking));

    return $this->success([
      'needs_categories' => false,
      'jobs' => $jobs,
    ]);
  }

  public function claimAvailableJob(Request $request, $id): JsonResponse
  {
    $vendor = $this->vendor($request);
    $claimed = false;
    $booking = null;

    DB::transaction(function () use ($id, $vendor, &$claimed, &$booking) {
      $booking = ServiceBooking::where('id', $id)
        ->where('vendor_id', 0)
        ->where('order_status', 'pending')
        ->lockForUpdate()
        ->first();

      if (!$booking) {
        return;
      }

      $booking->update(['vendor_id' => $vendor->id]);
      Vendor::where('id', $vendor->id)->increment('total_appointment');
      $claimed = true;
    });

    if (!$claimed) {
      return $this->error('This job is no longer available.', 409);
    }

    return $this->success(['appointment' => $this->appointmentPayload($booking->fresh())], 'Job claimed successfully.');
  }

  private function vendor(Request $request): Vendor
  {
    return $request->user();
  }

  private function appointmentQuery(int $vendorId, ?int $languageId)
  {
    return ServiceBooking::with(['staff', 'vendorInvoice', 'completionImages', 'serviceContent' => function ($query) use ($languageId) {
      $query->when($languageId, fn ($q) => $q->where('language_id', $languageId));
    }])->where('vendor_id', $vendorId);
  }

  private function appointmentForVendor(Request $request, $id): ServiceBooking
  {
    $languageId = optional(Language::where('is_default', 1)->first())->id;

    return $this->appointmentQuery($this->vendor($request)->id, $languageId)->findOrFail($id);
  }

  private function vendorPayload(Vendor $vendor): array
  {
    return [
      'id' => $vendor->id,
      'username' => $vendor->username,
      'email' => $vendor->email,
      'phone' => $vendor->phone,
      'status' => $vendor->status,
      'verification_status' => $vendor->verification_status,
      'verification_rejection_reason' => $vendor->verification_rejection_reason,
      'business_type' => $vendor->business_type,
      'right_to_work_is_british' => (bool) $vendor->right_to_work_is_british,
      'profile' => $vendor->vendor_info,
      'service_categories' => $vendor->relationLoaded('serviceCategories') ? $vendor->serviceCategories->values() : [],
    ];
  }

  private function documentPayload(VendorDocument $document): array
  {
    return [
      'id' => $document->id,
      'type' => $document->type,
      'status' => $document->status,
      'url' => $document->url,
      'policy_number' => $document->policy_number,
      'id_type' => $document->id_type,
      'expiry_date' => optional($document->expiry_date)->toDateString(),
      'date_of_birth' => optional($document->date_of_birth)->toDateString(),
      'rejection_reason' => $document->rejection_reason,
    ];
  }

  private function appointmentPayload(ServiceBooking $booking): array
  {
    return [
      'id' => $booking->id,
      'order_number' => $booking->order_number,
      'service_id' => $booking->service_id,
      'service_name' => optional($booking->serviceContent->first())->name,
      'customer_name' => $booking->customer_name,
      'customer_email' => $booking->customer_email,
      'customer_phone' => $booking->customer_phone,
      'site_name' => $booking->site_name,
      'site_phone' => $booking->site_phone,
      'site_email' => $booking->site_email,
      'site_address' => $booking->site_address,
      'site_zip_code' => $booking->site_zip_code,
      'site_country' => $booking->site_country,
      'booking_date' => $booking->booking_date,
      'booking_time' => $booking->booking_time,
      'order_status' => $booking->order_status,
      'payment_status' => $booking->payment_status,
      'refund' => $booking->refund,
      'customer_paid' => $booking->customer_paid,
      'currency_symbol' => $booking->currency_symbol,
      'staff_id' => $booking->staff_id,
      'completed_at' => optional($booking->completed_at)->toDateTimeString(),
      'job_certificate_url' => $booking->job_certificate ? asset('assets/file/certificates/' . $booking->job_certificate) : null,
      'invoice' => $booking->relationLoaded('vendorInvoice') ? $booking->vendorInvoice : null,
    ];
  }

  private function buildTasks(Vendor $vendor): array
  {
    $info = $vendor->vendor_info;
    $tasks = [
      ['key' => 'profile', 'status' => ($info && $info->name && $vendor->phone && $info->address) ? 'completed' : 'pending'],
      ['key' => 'category', 'status' => $vendor->serviceCategories->isNotEmpty() ? 'completed' : 'pending'],
      ['key' => 'postcode', 'status' => ($info && $info->postcode) ? 'completed' : 'pending'],
    ];

    foreach ($this->requiredDocTypes as $type) {
      if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && $vendor->right_to_work_is_british) {
        $tasks[] = ['key' => $type, 'status' => 'completed'];
        continue;
      }

      $document = $vendor->documents->firstWhere('type', $type);
      $tasks[] = ['key' => $type, 'status' => $document ? $document->status : 'pending'];
    }

    $tasks[] = ['key' => 'submit', 'status' => $vendor->verification_status === 'not_submitted' ? 'pending' : 'completed'];
    $tasks[] = ['key' => 'approval', 'status' => (int) $vendor->status === 1 ? 'approved' : ($vendor->verification_status ?: 'pending')];

    return $tasks;
  }

  private function canSubmitVerification(Vendor $vendor): bool
  {
    if ($vendor->serviceCategories->isEmpty() || !$vendor->vendor_info || empty($vendor->vendor_info->postcode)) {
      return false;
    }

    foreach ($this->requiredDocTypes as $type) {
      if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && $vendor->right_to_work_is_british) {
        continue;
      }

      $document = $vendor->documents->firstWhere('type', $type);
      if (!$document || $document->status === 'rejected' || empty($document->expiry_date)) {
        return false;
      }
      if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && empty($document->date_of_birth)) {
        return false;
      }
    }

    return true;
  }

  private function generateInvoiceNumber(): string
  {
    $prefix = 'IV-' . now()->format('y');
    $last = Invoice::where('invoice_number', 'like', $prefix . '%')
      ->lockForUpdate()
      ->orderByDesc('id')
      ->value('invoice_number');
    $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

    return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
  }

  private function success($data = null, string $message = 'Success.', int $status = 200): JsonResponse
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data,
    ], $status);
  }

  private function error(string $message, int $status = 400, array $errors = []): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => $message,
      'errors' => $errors,
    ], $status);
  }
}
