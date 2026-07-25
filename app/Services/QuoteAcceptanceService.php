<?php

namespace App\Services;

use App\Models\QuoteRequest;
use App\Models\QuoteStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Issues and verifies the single-use Accept/Decline link tokens emailed to a
 * customer once admin has priced their quote. Only a sha256 hash of the token
 * is ever persisted, the plain token only ever exists in the outgoing email,
 * and every state transition is wrapped in a locked DB transaction to prevent
 * double-accept/double-decline races.
 */
class QuoteAcceptanceService
{
  public const TOKEN_TTL_DAYS = 14;

  public function issueToken(): array
  {
    $plainToken = Str::random(64);

    return [
      'plain' => $plainToken,
      'hash' => hash('sha256', $plainToken),
      'expires_at' => now()->addDays(self::TOKEN_TTL_DAYS),
    ];
  }

  public function findByPlainToken(string $token): ?QuoteRequest
  {
    if (strlen($token) !== 64) {
      return null;
    }

    return QuoteRequest::query()
      ->with('service.content')
      ->where('response_token_hash', hash('sha256', $token))
      ->first();
  }

  public function isExpired(QuoteRequest $quote): bool
  {
    return !$quote->response_token_expires_at || $quote->response_token_expires_at->isPast();
  }

  public function isUsable(QuoteRequest $quote): bool
  {
    return $quote->status === QuoteRequest::STATUS_QUOTE_SENT
      && !$quote->response_token_used_at
      && !$this->isExpired($quote);
  }

  /**
   * Locks the quote, transitions it to accepted, and returns the fixed-price
   * cart item to push into session. Never recalculates the price — the
   * checkout total must equal exactly $quote->total_price (see the
   * price_status === 'quote_locked' guard in ServicePaymentController).
   *
   * Returns null if the token is no longer usable (already used/expired/wrong status).
   */
  public function accept(string $token): ?array
  {
    return DB::transaction(function () use ($token) {
      $quote = QuoteRequest::query()
        ->where('response_token_hash', hash('sha256', $token))
        ->lockForUpdate()
        ->first();

      if (!$quote || !$this->isUsable($quote)) {
        return null;
      }

      $this->recordTransition($quote, QuoteRequest::STATUS_CUSTOMER_ACCEPTED, 'Accepted by customer via emailed link.');

      $quote->forceFill([
        'status' => QuoteRequest::STATUS_CUSTOMER_ACCEPTED,
        'response_token_used_at' => now(),
        'customer_responded_at' => now(),
      ])->save();

      return $this->buildCartItem($quote);
    });
  }

  public function decline(string $token, ?string $reason = null): bool
  {
    return DB::transaction(function () use ($token, $reason) {
      $quote = QuoteRequest::query()
        ->where('response_token_hash', hash('sha256', $token))
        ->lockForUpdate()
        ->first();

      if (!$quote || !$this->isUsable($quote)) {
        return false;
      }

      $this->recordTransition($quote, QuoteRequest::STATUS_CUSTOMER_DECLINED, $reason ?: 'Declined by customer via emailed link.');

      $quote->forceFill([
        'status' => QuoteRequest::STATUS_CUSTOMER_DECLINED,
        'response_token_used_at' => now(),
        'customer_responded_at' => now(),
      ])->save();

      return true;
    });
  }

  private function recordTransition(QuoteRequest $quote, string $newStatus, ?string $note): void
  {
    QuoteStatusHistory::create([
      'quote_request_id' => $quote->id,
      'previous_status' => $quote->status,
      'new_status' => $newStatus,
      'note' => $note,
      'changed_by_type' => 'customer',
      'admin_id' => null,
    ]);
  }

  /**
   * Cart item shaped like ServiceCartController::quickAdd()'s item, with
   * price_status='quote_locked' so ServicePaymentController skips
   * recalculating from questions/postcode and uses total_price verbatim.
   */
  private function buildCartItem(QuoteRequest $quote): array
  {
    $itemKey = Str::uuid()->toString();
    $total = round((float) $quote->total_price, 2);

    return [
      $itemKey => [
        'key' => $itemKey,
        'item_type' => 'service',
        'service_id' => $quote->service_id,
        'service_name' => optional(optional($quote->service)->content->first())->name ?: 'Quoted service',
        'price' => $total,
        'base_price' => $total,
        'price_status' => 'quote_locked',
        'quote_request_id' => $quote->id,
        'booking_date' => null,
        'service_hour_id' => null,
        'start_time' => null,
        'end_time' => null,
        'staff_id' => $quote->assigned_staff_id,
        'max_person' => 1,
        'customer' => [
          'name' => $quote->customer_name,
          'phone' => $quote->customer_phone,
          'email' => $quote->customer_email,
          'address' => $quote->customer_address,
          'zip_code' => $quote->customer_postcode,
        ],
        'site' => [
          'address' => $quote->customer_address,
          'zip_code' => $quote->customer_postcode,
        ],
        'billing_same' => 1,
        'billing' => [],
        'alternate' => [],
        'user_id' => $quote->user_id ?: (Auth::guard('web')->check() ? Auth::guard('web')->id() : null),
        'vendor_id' => (int) ($quote->vendor_id ?? 0),
        'zoom_status' => 0,
        'calendar_status' => 0,
        'question_answers' => json_encode([]),
        'needs_checkout_details' => true,
      ],
    ];
  }
}
