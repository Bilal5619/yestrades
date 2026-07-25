<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Services\QuoteAcceptanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Handles the Accept/Decline link emailed to a customer once admin has priced
 * their quote. Hashed-token lookup + locked-transaction pattern (mirrors this
 * codebase's convention for single-use emailed links).
 */
class QuoteResponseController extends Controller
{
  public function show(string $token, QuoteAcceptanceService $quotes)
  {
    $quote = $quotes->findByPlainToken($token);
    abort_unless($quote, 404);

    $expired = $quotes->isExpired($quote);
    $used = (bool) $quote->response_token_used_at;

    return view('frontend.quote.respond', [
      'quote' => $quote,
      'token' => $token,
      'expired' => $expired,
      'used' => $used,
      'alreadyAccepted' => $quote->status === QuoteRequest::STATUS_CUSTOMER_ACCEPTED,
      'alreadyDeclined' => $quote->status === QuoteRequest::STATUS_CUSTOMER_DECLINED,
      'serviceName' => $quote->serviceName(),
    ]);
  }

  public function accept(string $token, QuoteAcceptanceService $quotes)
  {
    $item = $quotes->accept($token);

    if (!$item) {
      return redirect()->route('frontend.quote.response.show', $token)
        ->with('error', 'This quote link has already been used or has expired.');
    }

    // Replace any unrelated buy-service items so a locked quote total is
    // never combined with normal cart pricing in the same checkout.
    Session::put('serviceCart', $item);

    return redirect()->route('frontend.services.cart');
  }

  public function decline(Request $request, string $token, QuoteAcceptanceService $quotes)
  {
    $ok = $quotes->decline($token, $request->input('reason'));

    if (!$ok) {
      return redirect()->route('frontend.quote.response.show', $token)
        ->with('error', 'This quote link has already been used or has expired.');
    }

    return redirect()->route('frontend.quote.response.show', $token)
      ->with('success', 'You have declined this quote. Thank you for letting us know.');
  }
}
