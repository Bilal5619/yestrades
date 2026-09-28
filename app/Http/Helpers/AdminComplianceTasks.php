<?php

namespace App\Http\Helpers;

use App\Models\Services\ServiceBooking;
use App\Models\VendorDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Admin compliance / action centre. Surfaces the open tasks an admin needs to
 * act on, derived from vendor and job state:
 *   - uploaded vendor documents awaiting approval
 *   - expired vendor documents (e.g. lapsed insurance)
 *   - jobs awaiting a payment decision
 *   - jobs that still need a vendor assigned (find/assign a vendor)
 *   - vendor availability conflicts (assigned vendor unavailable for the slot)
 */
class AdminComplianceTasks
{
  private const SECTION_LIMIT = 50;

  /**
   * Vendor-uploaded documents that still need admin approval/rejection.
   */
  public static function pendingDocumentApprovals($limit = self::SECTION_LIMIT)
  {
    return self::pendingDocumentApprovalsQuery()
      ->with(['vendor.vendor_info'])
      ->orderBy('created_at')
      ->limit($limit)
      ->get();
  }

  public static function pendingDocumentApprovalsCount(): int
  {
    return self::pendingDocumentApprovalsQuery()->count();
  }

  private static function pendingDocumentApprovalsQuery()
  {
    return VendorDocument::whereIn(DB::raw('LOWER(status)'), [
      VendorDocument::STATUS_PENDING,
      VendorDocument::STATUS_UNDER_REVIEW,
    ])
      ->whereHas('vendor');
  }

  /**
   * Vendor documents that carry an expiry date which has now passed.
   */
  public static function expiredDocuments($limit = self::SECTION_LIMIT)
  {
    return self::expiredDocumentsQuery()
      ->with(['vendor.vendor_info'])
      ->orderBy('expiry_date')
      ->limit($limit)
      ->get();
  }

  public static function expiredDocumentsCount(): int
  {
    return self::expiredDocumentsQuery()->count();
  }

  private static function expiredDocumentsQuery()
  {
    return VendorDocument::whereNotNull('expiry_date')
      ->whereDate('expiry_date', '<', Carbon::today()->toDateString())
      ->whereHas('vendor');
  }

  /**
   * Jobs awaiting a payment decision by the admin.
   */
  public static function pendingPayments($languageId = null, $limit = self::SECTION_LIMIT)
  {
    return self::pendingPaymentsQuery()
      ->with(['vendor', 'vendorInfo', 'serviceContent' => function ($q) use ($languageId) {
        $q->where('language_id', $languageId);
      }])
      ->orderByDesc('id')
      ->limit($limit)
      ->get();
  }

  public static function pendingPaymentsCount(): int
  {
    return self::pendingPaymentsQuery()->count();
  }

  private static function pendingPaymentsQuery()
  {
    return ServiceBooking::where('payment_status', 'pending')
      ->where('order_status', '!=', 'rejected');
  }

  /**
   * Jobs that still need a vendor assigned (admin must find/assign a vendor).
   */
  public static function jobsNeedingVendor($languageId = null, $limit = self::SECTION_LIMIT)
  {
    return self::jobsNeedingVendorQuery()
      ->with(['serviceContent' => function ($q) use ($languageId) {
        $q->where('language_id', $languageId);
      }])
      ->orderByDesc('id')
      ->limit($limit)
      ->get();
  }

  public static function jobsNeedingVendorCount(): int
  {
    return self::jobsNeedingVendorQuery()->count();
  }

  private static function jobsNeedingVendorQuery()
  {
    return ServiceBooking::where('vendor_id', 0)
      ->whereNotIn('order_status', ['rejected', 'completed']);
  }

  /**
   * Per-section counts plus the overall open total (used by the sidebar badge
   * and dashboard card).
   */
  public static function counts(): array
  {
    $pendingDocumentApprovals = self::pendingDocumentApprovalsCount();
    $expired = self::expiredDocumentsCount();
    $payments = self::pendingPaymentsCount();
    $unassigned = self::jobsNeedingVendorCount();
    $availability = VendorAvailability::unresolvedConflictCount();
    $certificatesExpiring = CertificateExpiry::expiringSoonCount();
    $certificatesExpired = CertificateExpiry::expiredCount();
    // Completed jobs with no certificate file, or no expiry date on it.
    $certificatesMissingDate = CertificateExpiry::missingExpiryCount();

    return [
      'pending_document_approvals' => $pendingDocumentApprovals,
      'expired_documents' => $expired,
      'pending_payments' => $payments,
      'jobs_needing_vendor' => $unassigned,
      'availability_conflicts' => $availability,
      'certificates_expiring' => $certificatesExpiring,
      'certificates_expired' => $certificatesExpired,
      'certificates_missing_date' => $certificatesMissingDate,
      'total' => $pendingDocumentApprovals + $expired + $payments + $unassigned + $availability + $certificatesExpiring + $certificatesExpired + $certificatesMissingDate,
    ];
  }

  public static function openCount(): int
  {
    return self::counts()['total'];
  }
}
