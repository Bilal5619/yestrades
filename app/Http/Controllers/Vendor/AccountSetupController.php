<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\UploadFile;
use App\Models\BasicSettings\Basic;
use App\Models\Vendor;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountSetupController extends Controller
{
  private array $requiredDocTypes = [
    VendorDocument::TYPE_INSURANCE,
    VendorDocument::TYPE_ID,
    VendorDocument::TYPE_RIGHT_TO_WORK,
  ];

  public function index()
  {
    $vendor = Vendor::with(['vendor_info', 'serviceCategories', 'documents'])
      ->findOrFail(Auth::guard('vendor')->id());

    $information['vendor'] = $vendor;
    $information['registrationNumbers'] = $vendor->registrationNumbers()->orderBy('id')->get();
    $information['accreditations'] = $information['registrationNumbers']
      ->where('category', 'accreditation')
      ->values();
    $information['tasks'] = $this->buildTasks($vendor, $information['accreditations']->isNotEmpty());
    $information['documents'] = $vendor->documents->keyBy('type');
    $information['canSubmit'] = $this->canSubmitVerification($vendor);

    return view('vendors.account-setup.index', $information);
  }

  /**
   * Upload (or re-upload) one of the three required documents.
   */
  public function storeDocument(Request $request)
  {
    $vendorId = Auth::guard('vendor')->id();
    $vendor = Vendor::findOrFail($vendorId);

    $type = $request->input('type');
    if (!in_array($type, $this->requiredDocTypes, true)) {
      return redirect()->back()->with('warning', 'Unknown document type.');
    }

    $existing = VendorDocument::where('vendor_id', $vendorId)->where('type', $type)->first();
    if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && $request->boolean('right_to_work_is_british')) {
      $vendor->update(['right_to_work_is_british' => true]);

      return redirect()->back()->with('success', 'Right to Work if not British marked as not required for British vendors.');
    }

    if ($type === VendorDocument::TYPE_RIGHT_TO_WORK) {
      $vendor->update(['right_to_work_is_british' => false]);
    }

    $rules = [
      'file' => [
        (!$existing || $existing->is_rejected || $existing->is_approved) ? 'required' : 'nullable',
        'file',
        'mimes:pdf,jpg,jpeg,png',
        'max:10240'
      ],
    ];
    $attributes = [
      'vendor_id' => $vendorId,
      'type'      => $type,
      'status'    => VendorDocument::STATUS_UNDER_REVIEW,
      'rejection_reason' => null,
      'id_type'   => null,
      'policy_number' => null,
      'expiry_date' => null,
      'date_of_birth' => null,
    ];

    if ($type === VendorDocument::TYPE_INSURANCE) {
      $rules['policy_number'] = ['nullable', 'string', 'max:255'];
      $rules['expiry_date'] = ['required', 'date'];
      $attributes['policy_number'] = $request->policy_number;
      $attributes['expiry_date'] = $request->expiry_date;
    } elseif ($type === VendorDocument::TYPE_ID) {
      $rules['id_type'] = ['required', Rule::in(['driving_licence', 'passport'])];
      $rules['expiry_date'] = ['required', 'date'];
      $attributes['id_type'] = $request->id_type;
      $attributes['expiry_date'] = $request->expiry_date;
    } elseif ($type === VendorDocument::TYPE_RIGHT_TO_WORK) {
      $rules['expiry_date'] = ['required', 'date'];
      $rules['date_of_birth'] = ['required', 'date', 'before:today'];
      $attributes['expiry_date'] = $request->expiry_date;
      $attributes['date_of_birth'] = $request->date_of_birth;
    }

    $request->validate($rules);

    if ($request->hasFile('file')) {
      $fileName = UploadFile::store(public_path(VendorDocument::STORAGE_DIR), $request->file('file'));
      // remove the previous file on re-upload
      if ($existing && $existing->file_path) {
        @unlink(public_path(VendorDocument::STORAGE_DIR . $existing->file_path));
      }
      $attributes['file_path'] = $fileName;
    } elseif ($existing) {
      $attributes['file_path'] = $existing->file_path;
    }

    VendorDocument::updateOrCreate(
      ['vendor_id' => $vendorId, 'type' => $type],
      $attributes
    );

    return redirect()->back()->with('success', 'Document uploaded. It is now under review.');
  }

  /**
   * Vendor submits the whole account for admin approval.
   */
  public function submitVerification()
  {
    $vendor = Vendor::with(['serviceCategories', 'documents', 'vendor_info'])
      ->findOrFail(Auth::guard('vendor')->id());

    if (!$this->canSubmitVerification($vendor)) {
      return redirect()->back()->with('warning', 'Please complete all required setup tasks before submitting.');
    }

    if (in_array($vendor->verification_status, ['submitted', 'approved'], true)) {
      return redirect()->back()->with('warning', 'Your verification has already been submitted.');
    }

    $vendor->update([
      'verification_status' => 'submitted',
      'verification_rejection_reason' => null,
    ]);

    $this->notifyAdminOfSubmission($vendor);

    return redirect()->back()->with('success', 'Verification submitted. Approval can take up to 24 hours.');
  }

  /**
   * Notify the platform inbox that a vendor has completed setup and submitted
   * their verification for review.
   */
  private function notifyAdminOfSubmission(Vendor $vendor): void
  {
    $vendorName = optional($vendor->vendor_info)->name ?: $vendor->username;
    $categories = $vendor->serviceCategories->pluck('name')->join(', ');
    $postcode = optional($vendor->vendor_info)->postcode;
    $detailsUrl = route('admin.vendor_management.vendor_details', ['id' => $vendor->id]);

    $mailData = [
      'recipient' => Basic::value('to_mail') ?? config('mail.from.address'),
      'subject'   => 'Vendor verification submitted: ' . $vendorName,
      'body'      => '<p>A vendor has completed their account setup and submitted their verification for review.</p>'
        . '<ul>'
        . '<li><strong>Vendor:</strong> ' . e($vendorName) . ' (' . e($vendor->username) . ')</li>'
        . '<li><strong>Email:</strong> ' . e($vendor->email) . '</li>'
        . ($vendor->phone ? '<li><strong>Phone:</strong> ' . e($vendor->phone) . '</li>' : '')
        . ($categories ? '<li><strong>Service categories:</strong> ' . e($categories) . '</li>' : '')
        . ($postcode ? '<li><strong>Service area postcode:</strong> ' . e($postcode) . '</li>' : '')
        . '</ul>'
        . '<p>Documents submitted: Public Liability Insurance, Driving Licence/Passport, Right to Work if not British.</p>'
        . '<p><a href="' . e($detailsUrl) . '">Review this vendor in the admin panel</a></p>',
    ];

    try {
      BasicMailer::sendMail($mailData);
    } catch (\Throwable $e) {
      // Don't let a mail failure block the vendor's submission.
    }
  }

  /**
   * Build the ordered checklist with each task's status.
   */
  private function buildTasks(Vendor $vendor, bool $hasAccreditation = false): array
  {
    $info = $vendor->vendor_info;
    $profileDone = $info && !empty($info->name) && !empty($vendor->phone) && !empty($info->address);
    $categoryDone = $vendor->serviceCategories->isNotEmpty();
    $postcodeDone = $info && !empty($info->postcode);

    $tasks = [
      ['key' => 'profile', 'label' => 'Complete profile details', 'status' => $profileDone ? 'completed' : 'pending'],
      ['key' => 'category', 'label' => 'Select service type/category', 'status' => $categoryDone ? 'completed' : 'pending'],
      ['key' => 'postcode', 'label' => 'Add service area postcode', 'status' => $postcodeDone ? 'completed' : 'pending'],
      $this->docTask($vendor, VendorDocument::TYPE_INSURANCE, 'Upload Public Liability Insurance'),
      $this->docTask($vendor, VendorDocument::TYPE_ID, 'Upload Driving Licence or Passport'),
      $this->rightToWorkTask($vendor),
      [
        'key' => 'accreditation',
        'label' => 'Accreditation certification (optional)',
        'status' => $hasAccreditation ? 'completed' : 'optional',
      ],
    ];

    // Submit verification
    $submitStatus = $vendor->verification_status === 'not_submitted' && (int) $vendor->status !== 1
      ? 'pending'
      : 'completed';
    $tasks[] = ['key' => 'submit', 'label' => 'Submit verification', 'status' => $submitStatus];

    // Waiting for admin approval
    $approvalStatus = 'pending';
    if ($vendor->verification_status === 'approved' || (int) $vendor->status === 1) {
      $approvalStatus = 'approved';
    } elseif ($vendor->verification_status === 'rejected') {
      $approvalStatus = 'rejected';
    } elseif ($vendor->verification_status === 'submitted') {
      $approvalStatus = 'under_review';
    }
    $tasks[] = ['key' => 'approval', 'label' => 'Waiting for admin approval', 'status' => $approvalStatus];

    return $tasks;
  }

  private function docTask(Vendor $vendor, string $type, string $label): array
  {
    $doc = $vendor->documents->firstWhere('type', $type);
    $status = 'pending';
    if ($doc) {
      $status = $doc->status; // under_review | approved | rejected | pending
    }
    return ['key' => $type, 'label' => $label, 'status' => $status, 'document' => $doc];
  }

  private function rightToWorkTask(Vendor $vendor): array
  {
    if ($vendor->right_to_work_is_british) {
      return [
        'key' => VendorDocument::TYPE_RIGHT_TO_WORK,
        'label' => 'Right to Work if not British (not required for British vendors)',
        'status' => 'completed',
        'document' => null,
      ];
    }

    return $this->docTask($vendor, VendorDocument::TYPE_RIGHT_TO_WORK, 'Upload Right to Work if not British');
  }

  /**
   * Required to submit: categories chosen, postcode set, and all three docs uploaded
   * (not currently rejected).
   */
  private function canSubmitVerification(Vendor $vendor): bool
  {
    if ($vendor->serviceCategories->isEmpty()) {
      return false;
    }
    if (!$vendor->vendor_info || empty($vendor->vendor_info->postcode)) {
      return false;
    }
    foreach ($this->requiredDocTypes as $type) {
      if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && $vendor->right_to_work_is_british) {
        continue;
      }

      $doc = $vendor->documents->firstWhere('type', $type);
      if (!$doc || $doc->status === 'rejected') {
        return false;
      }
      if (in_array($type, [VendorDocument::TYPE_INSURANCE, VendorDocument::TYPE_ID, VendorDocument::TYPE_RIGHT_TO_WORK], true) && empty($doc->expiry_date)) {
        return false;
      }
      if ($type === VendorDocument::TYPE_RIGHT_TO_WORK && empty($doc->date_of_birth)) {
        return false;
      }
    }
    return true;
  }
}
