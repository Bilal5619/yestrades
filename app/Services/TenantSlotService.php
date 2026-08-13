<?php

namespace App\Services;

use App\Http\Helpers\BasicMailer;
use App\Models\Services\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantSlotService
{
  public const STATUS_WAITING = 'waiting_tenant_slot';
  public const STATUS_SELECTED = 'selected';
  public const STATUS_SELECTED_PENDING_OWNER = 'tenant_selected_pending_owner_confirmation';
  public const STATUS_CONFIRMED = 'appointment_confirmed';
  public const STATUS_EXPIRED = 'waiting_tenant_slot_expired';

  public function minimumDate(): Carbon
  {
    return Carbon::today()->addDays(3);
  }

  public function normalSlots(): array
  {
    return [
      ['value' => '08:00 AM - 10:00 AM', 'label' => '08:00 AM - 10:00 AM', 'start' => '08:00:00', 'end' => '10:00:00'],
      ['value' => '10:00 AM - 12:00 PM', 'label' => '10:00 AM - 12:00 PM', 'start' => '10:00:00', 'end' => '12:00:00'],
      ['value' => '02:00 PM - 04:00 PM', 'label' => '02:00 PM - 04:00 PM', 'start' => '14:00:00', 'end' => '16:00:00'],
      ['value' => '04:00 PM - 06:00 PM', 'label' => '04:00 PM - 06:00 PM', 'start' => '16:00:00', 'end' => '18:00:00'],
    ];
  }

  public function resolveNormalSlot(string $value): ?array
  {
    return collect($this->normalSlots())->firstWhere('value', $value);
  }

  public function isNormalBookingDate(Carbon $date): bool
  {
    return !$date->lt($this->minimumDate()) && !$date->isSunday();
  }

  public function issueToken(): array
  {
    $plainToken = Str::random(64);

    return [
      'plain' => $plainToken,
      'hash' => hash('sha256', $plainToken),
      'expires_at' => now()->addDays(3),
    ];
  }

  public function issueOwnerConfirmationToken(): array
  {
    $plainToken = Str::random(64);

    return [
      'plain' => $plainToken,
      'hash' => hash('sha256', $plainToken),
    ];
  }

  public function findByPlainToken(string $token): ?ServiceBooking
  {
    if (strlen($token) !== 64) {
      return null;
    }

    return ServiceBooking::query()
      ->with(['service.content', 'user', 'vendor'])
      ->where('tenant_slot_token_hash', hash('sha256', $token))
      ->first();
  }

  public function findByOwnerConfirmationToken(string $token): ?ServiceBooking
  {
    if (strlen($token) !== 64) {
      return null;
    }

    return ServiceBooking::query()
      ->with(['service.content', 'user', 'vendor'])
      ->where('tenant_owner_confirmation_token_hash', hash('sha256', $token))
      ->first();
  }

  public function isExpired(ServiceBooking $booking): bool
  {
    return !$booking->tenant_slot_expires_at || Carbon::parse($booking->tenant_slot_expires_at)->isPast();
  }

  public function expire(ServiceBooking $booking, bool $notify = true): bool
  {
    if ($booking->tenant_slot_status !== self::STATUS_WAITING || !$this->isExpired($booking)) {
      return false;
    }

    $booking->forceFill(['tenant_slot_status' => self::STATUS_EXPIRED])->save();

    if ($notify) {
      $this->notifyCustomerExpired($booking);
    }

    return true;
  }

  public function sendInvitations(Collection $bookings): void
  {
    $bookings->each(function (ServiceBooking $booking) {
      $plainToken = $booking->getAttribute('tenant_slot_plain_token');

      if (!$plainToken) {
        return;
      }

      $recipient = $booking->tenant_email ?: optional($booking->user)->email;

      if (!$recipient) {
        if ($booking->tenant_phone) {
          Log::info('Tenant slot link ready, but no SMS/WhatsApp provider is configured', [
            'booking_id' => $booking->id,
            'tenant_phone' => $booking->tenant_phone,
          ]);
        }
        return;
      }

      $serviceName = $this->serviceName($booking);
      $link = route('tenant.slot.show', ['token' => $plainToken]);
      $note = $booking->tenant_note
        ? '<p><strong>Message from the customer:</strong><br>' . nl2br(e($booking->tenant_note)) . '</p>'
        : '';

      $isTenantRecipient = !empty($booking->tenant_email);
      $greetingName = $isTenantRecipient ? $booking->tenant_name : $booking->customer_name;
      $intro = $isTenantRecipient
        ? 'You have been asked to choose a normal appointment date and time'
        : 'No SMS/WhatsApp provider is configured. Please share this secure appointment link with ' . e($booking->tenant_name);

      BasicMailer::sendMail([
        'recipient' => $recipient,
        'subject' => ($isTenantRecipient ? 'Choose' : 'Share') . ' the appointment date and time for ' . $serviceName,
        'body' => '<p>Hello ' . e($greetingName) . ',</p>'
          . '<p>' . $intro . ' for <strong>' . e($serviceName) . '</strong>.</p>'
          . $note
          . '<p><a href="' . e($link) . '">Choose appointment date and time</a></p>'
          . '<p>This secure link expires in 3 days and can only be used once.</p>',
      ]);
    });
  }

  public function notifySelection(ServiceBooking $booking): void
  {
    $plainToken = $booking->getAttribute('tenant_owner_confirmation_plain_token');
    $recipient = optional($booking->user)->email ?: $booking->customer_email;

    if (!$plainToken || !$recipient) {
      Log::warning('Owner confirmation email could not be sent for a tenant slot selection', [
        'booking_id' => $booking->id,
        'has_confirmation_token' => !empty($plainToken),
        'has_owner_email' => !empty($recipient),
      ]);
      return;
    }

    $serviceName = $this->serviceName($booking);
    $date = Carbon::parse($booking->booking_date)->format('j F Y');
    $time = Carbon::parse($booking->start_date)->format('g:i A') . ' - ' . Carbon::parse($booking->end_date)->format('g:i A');
    $address = collect([$booking->site_address ?: $booking->customer_address, $booking->site_zip_code ?: $booking->customer_zip_code])
      ->filter()->join(', ');
    $confirmationLink = route('tenant.slot.owner.show', ['token' => $plainToken]);
    $tenantNote = $booking->tenant_note
      ? '<p><strong>Tenant note:</strong><br>' . nl2br(e($booking->tenant_note)) . '</p>'
      : '';

    BasicMailer::sendMail([
      'recipient' => $recipient,
      'subject' => 'Tenant selected appointment date for your Yes Trades booking',
      'body' => '<p>Your tenant/occupier has selected an appointment time for your booking. Please review the details below.</p>'
        . '<p><strong>Service:</strong> ' . e($serviceName)
        . '<br><strong>Property:</strong> ' . e($address)
        . '<br><strong>Tenant/occupier:</strong> ' . e($booking->tenant_name)
        . '<br><strong>Selected date:</strong> ' . e($date)
        . '<br><strong>Selected time:</strong> ' . e($time)
        . '<br><strong>Booking reference:</strong> ' . e($booking->order_number) . '</p>'
        . $tenantNote
        . '<p><a href="' . e($confirmationLink) . '">Confirm appointment</a></p>'
        . '<p>The appointment remains pending until you confirm it.</p>',
    ]);
  }

  public function notifyAppointmentConfirmed(ServiceBooking $booking): void
  {
    $serviceName = $this->serviceName($booking);
    $date = Carbon::parse($booking->booking_date)->format('j F Y');
    $time = Carbon::parse($booking->start_date)->format('g:i A') . ' - ' . Carbon::parse($booking->end_date)->format('g:i A');
    $address = collect([$booking->site_address ?: $booking->customer_address, $booking->site_zip_code ?: $booking->customer_zip_code])
      ->filter()->join(', ');
    $subject = 'Appointment confirmed for your Yes Trades booking';
    $body = '<p>The tenant/occupier appointment has been confirmed.</p>'
      . '<p><strong>Service:</strong> ' . e($serviceName)
      . '<br><strong>Property:</strong> ' . e($address)
      . '<br><strong>Date:</strong> ' . e($date)
      . '<br><strong>Time:</strong> ' . e($time)
      . '<br><strong>Booking reference:</strong> ' . e($booking->order_number) . '</p>';

    $recipients = collect([
      optional($booking->user)->email ?: $booking->customer_email,
      $booking->tenant_email,
      optional($booking->vendor)->email,
    ])->filter()->unique();

    $recipients->each(function ($recipient) use ($subject, $body) {
      BasicMailer::sendMail(compact('recipient', 'subject', 'body'));
    });
  }

  public function notifyCustomerExpired(ServiceBooking $booking): void
  {
    $recipient = optional($booking->user)->email ?: $booking->customer_email;

    if (!$recipient) {
      return;
    }

    BasicMailer::sendMail([
      'recipient' => $recipient,
      'subject' => 'Tenant appointment link expired for ' . $booking->order_number,
      'body' => '<p>The tenant/occupier did not choose an appointment date and time within 3 days.</p>'
        . '<p>Please sign in to your customer account or contact us to request a new appointment link.</p>',
    ]);
  }

  public function serviceName(ServiceBooking $booking): string
  {
    if ($booking->package_name) {
      return $booking->package_name;
    }

    return optional(optional($booking->service)->content->first())->name ?: 'Service appointment';
  }
}
