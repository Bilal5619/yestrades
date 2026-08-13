<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Services\ServiceBooking;
use App\Services\TenantSlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TenantSlotController extends Controller
{
  public function show(string $token, TenantSlotService $tenantSlots)
  {
    $booking = $tenantSlots->findByPlainToken($token);

    abort_unless($booking, 404);

    if ($booking->tenant_slot_status === TenantSlotService::STATUS_WAITING && $tenantSlots->isExpired($booking)) {
      $tenantSlots->expire($booking);
      $booking->refresh();
    }

    return view('frontend.tenant.select-slot', [
      'booking' => $booking,
      'token' => $token,
      'serviceName' => $tenantSlots->serviceName($booking),
      'normalSlots' => $tenantSlots->normalSlots(),
      'minimumDate' => $tenantSlots->minimumDate()->format('Y-m-d'),
      'expired' => $booking->tenant_slot_status === TenantSlotService::STATUS_EXPIRED,
      'selected' => in_array($booking->tenant_slot_status, [
        TenantSlotService::STATUS_SELECTED,
        TenantSlotService::STATUS_SELECTED_PENDING_OWNER,
        TenantSlotService::STATUS_CONFIRMED,
      ], true),
      'awaitingOwnerConfirmation' => $booking->tenant_slot_status === TenantSlotService::STATUS_SELECTED_PENDING_OWNER,
      'confirmed' => in_array($booking->tenant_slot_status, [TenantSlotService::STATUS_SELECTED, TenantSlotService::STATUS_CONFIRMED], true),
    ]);
  }

  public function store(Request $request, string $token, TenantSlotService $tenantSlots)
  {
    $validator = Validator::make($request->all(), [
      'booking_date' => ['required', 'date_format:Y-m-d'],
      'time_slot' => ['required', 'string'],
    ]);

    if ($validator->fails()) {
      return back()->withErrors($validator)->withInput();
    }

    $date = Carbon::createFromFormat('Y-m-d', $request->input('booking_date'))->startOfDay();
    $slot = $tenantSlots->resolveNormalSlot($request->input('time_slot'));
    $bookingPreview = $tenantSlots->findByPlainToken($token);

    abort_unless($bookingPreview, 404);

    if (!$tenantSlots->isNormalBookingDate($date)) {
      return back()->withErrors([
        'booking_date' => __('Please choose Monday to Saturday, at least 3 days from today.'),
      ])->withInput();
    }

    if (!$slot) {
      return back()->withErrors([
        'time_slot' => __('Please choose one of the available normal appointment slots.'),
      ])->withInput();
    }

    if ($bookingPreview->vendor_id != 0) {
      $membershipExpiry = checkMembersipExpireDate($bookingPreview->vendor_id);

      if (!$membershipExpiry || $date->gt(Carbon::parse($membershipExpiry)->startOfDay())) {
        return back()->withErrors([
          'booking_date' => __('This provider is not available on that date. Please ask the landlord/customer to contact support.'),
        ])->withInput();
      }
    }

    $ownerConfirmationToken = $tenantSlots->issueOwnerConfirmationToken();

    $result = DB::transaction(function () use ($token, $tenantSlots, $date, $slot, $ownerConfirmationToken) {
      $booking = ServiceBooking::query()
        ->where('tenant_slot_token_hash', hash('sha256', $token))
        ->lockForUpdate()
        ->first();

      if (!$booking) {
        return 'missing';
      }

      if (in_array($booking->tenant_slot_status, [
        TenantSlotService::STATUS_SELECTED,
        TenantSlotService::STATUS_SELECTED_PENDING_OWNER,
        TenantSlotService::STATUS_CONFIRMED,
      ], true) || $booking->tenant_slot_used_at) {
        return 'used';
      }

      if ($booking->tenant_slot_status !== TenantSlotService::STATUS_WAITING || $tenantSlots->isExpired($booking)) {
        return [
          'status' => 'expired',
          'newly_expired' => $tenantSlots->expire($booking, false),
          'booking' => $booking,
        ];
      }

      $booking->forceFill([
        'booking_date' => $date->format('Y-m-d'),
        'start_date' => $slot['start'],
        'end_date' => $slot['end'],
        'service_hour_id' => null,
        'tenant_slot_status' => TenantSlotService::STATUS_SELECTED_PENDING_OWNER,
        'tenant_slot_selected_at' => now(),
        'tenant_slot_used_at' => now(),
        'tenant_owner_confirmation_token_hash' => $ownerConfirmationToken['hash'],
        'tenant_owner_confirmation_sent_at' => now(),
        'tenant_owner_confirmed_at' => null,
        'order_status' => 'pending',
      ])->save();

      $booking->setAttribute('tenant_owner_confirmation_plain_token', $ownerConfirmationToken['plain']);

      return $booking;
    });

    if ($result === 'missing') {
      abort(404);
    }

    if (is_array($result) && ($result['status'] ?? null) === 'expired') {
      if ($result['newly_expired'] ?? false) {
        $result['booking']->loadMissing('user');
        $tenantSlots->notifyCustomerExpired($result['booking']);
      }
      return redirect()->route('tenant.slot.show', ['token' => $token]);
    }

    if ($result === 'used') {
      return redirect()->route('tenant.slot.show', ['token' => $token]);
    }

    $result->loadMissing(['service.content', 'user', 'vendor']);
    $tenantSlots->notifySelection($result);

    return redirect()->route('tenant.slot.show', ['token' => $token])
      ->with('success', __('Your appointment date and time have been saved and sent to the landlord/customer for confirmation.'));
  }

  public function showOwnerConfirmation(string $token, TenantSlotService $tenantSlots)
  {
    $booking = $tenantSlots->findByOwnerConfirmationToken($token);

    abort_unless($booking, 404);

    return view('frontend.tenant.confirm-slot', [
      'booking' => $booking,
      'token' => $token,
      'serviceName' => $tenantSlots->serviceName($booking),
      'pending' => $booking->tenant_slot_status === TenantSlotService::STATUS_SELECTED_PENDING_OWNER,
      'confirmed' => in_array($booking->tenant_slot_status, [TenantSlotService::STATUS_SELECTED, TenantSlotService::STATUS_CONFIRMED], true),
    ]);
  }

  public function confirmOwnerSelection(string $token, TenantSlotService $tenantSlots)
  {
    $result = DB::transaction(function () use ($token) {
      $booking = ServiceBooking::query()
        ->where('tenant_owner_confirmation_token_hash', hash('sha256', $token))
        ->lockForUpdate()
        ->first();

      if (!$booking) {
        return 'missing';
      }

      if (in_array($booking->tenant_slot_status, [TenantSlotService::STATUS_SELECTED, TenantSlotService::STATUS_CONFIRMED], true)) {
        return 'confirmed';
      }

      if ($booking->tenant_slot_status !== TenantSlotService::STATUS_SELECTED_PENDING_OWNER) {
        return 'unavailable';
      }

      $booking->forceFill([
        'tenant_slot_status' => TenantSlotService::STATUS_CONFIRMED,
        'tenant_owner_confirmed_at' => now(),
      ])->save();

      return $booking;
    });

    if ($result === 'missing') {
      abort(404);
    }

    if ($result === 'confirmed') {
      return redirect()->route('tenant.slot.owner.show', ['token' => $token]);
    }

    if ($result === 'unavailable') {
      return redirect()->route('tenant.slot.owner.show', ['token' => $token])
        ->with('error', __('This appointment selection can no longer be confirmed.'));
    }

    $result->loadMissing(['service.content', 'user', 'vendor']);
    $tenantSlots->notifyAppointmentConfirmed($result);

    return redirect()->route('tenant.slot.owner.show', ['token' => $token])
      ->with('success', __('The appointment date and time are confirmed.'));
  }
}
