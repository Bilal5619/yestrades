<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\QuoteRequest;
use App\Models\QuoteStatusHistory;
use App\Models\Services\ServiceContent;
use App\Models\Staff\Staff;
use App\Services\QuoteAcceptanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuoteRequestController extends Controller
{
  public function index(Request $request)
  {
    $languageId = Language::where('is_default', 1)->value('id');

    $query = QuoteRequest::query()
      ->with(['assignedStaff', 'service.content' => function ($q) use ($languageId) {
        $q->where('language_id', $languageId);
      }])
      ->latest();

    if ($request->filled('status')) {
      $query->where('status', $request->input('status'));
    }

    $quotes = $query->paginate(20)->withQueryString();
    $statuses = QuoteRequest::STATUS_LABELS;
    $selectedStatus = $request->input('status');

    return view('admin.quote-requests.index', compact('quotes', 'statuses', 'selectedStatus'));
  }

  public function show($id)
  {
    $quote = QuoteRequest::with(['answers.question', 'attachments', 'statusHistory.admin', 'assignedStaff', 'service'])->findOrFail($id);
    $statuses = QuoteRequest::STATUS_LABELS;
    $staffMembers = Staff::orderBy('username')->get(['id', 'username']);

    return view('admin.quote-requests.show', compact('quote', 'statuses', 'staffMembers'));
  }

  public function updateStatus(Request $request, $id)
  {
    $quote = QuoteRequest::findOrFail($id);

    $request->validate([
      'status' => ['required', Rule::in(QuoteRequest::STATUSES)],
      'note' => 'nullable|string|max:2000',
    ]);

    if (in_array($quote->status, QuoteRequest::LOCKED_STATUSES, true)) {
      return back()->with('error', 'This quote request is locked and its status can no longer be changed.');
    }

    $newStatus = $request->input('status');

    QuoteStatusHistory::create([
      'quote_request_id' => $quote->id,
      'previous_status' => $quote->status,
      'new_status' => $newStatus,
      'note' => $request->input('note'),
      'changed_by_type' => 'admin',
      'admin_id' => Auth::guard('admin')->id(),
    ]);

    $quote->update(['status' => $newStatus]);

    return back()->with('success', 'Quote status updated.');
  }

  public function assignStaff(Request $request, $id)
  {
    $quote = QuoteRequest::findOrFail($id);

    $request->validate([
      'staff_id' => 'nullable|exists:staff,id',
    ]);

    $quote->update(['assigned_staff_id' => $request->input('staff_id') ?: null]);

    return back()->with('success', 'Staff assignment updated.');
  }

  public function savePricing(Request $request, $id)
  {
    $quote = QuoteRequest::findOrFail($id);

    if (in_array($quote->status, QuoteRequest::LOCKED_STATUSES, true)) {
      return back()->with('error', 'This quote request is locked and can no longer be priced.');
    }

    $data = $request->validate([
      'labour_cost' => 'nullable|numeric|min:0',
      'material_cost' => 'nullable|numeric|min:0',
      'callout_charge' => 'nullable|numeric|min:0',
      'additional_charges' => 'nullable|numeric|min:0',
      'discount' => 'nullable|numeric|min:0',
      'vat_amount' => 'nullable|numeric|min:0',
      'expiry_date' => 'nullable|date',
      'expected_duration' => 'nullable|string|max:255',
      'customer_notes' => 'nullable|string|max:5000',
      'internal_notes' => 'nullable|string|max:5000',
    ]);

    // Server calculates the total itself — never trust a client-submitted total.
    $total = round(
      (float) ($data['labour_cost'] ?? 0)
      + (float) ($data['material_cost'] ?? 0)
      + (float) ($data['callout_charge'] ?? 0)
      + (float) ($data['additional_charges'] ?? 0)
      + (float) ($data['vat_amount'] ?? 0)
      - (float) ($data['discount'] ?? 0),
      2
    );

    $data['total_price'] = max($total, 0);

    // Advance to "quote_prepared" only if pricing hasn't already moved further along.
    if (in_array($quote->status, [QuoteRequest::STATUS_NEW, QuoteRequest::STATUS_UNDER_REVIEW, QuoteRequest::STATUS_MORE_INFO_REQUIRED, QuoteRequest::STATUS_SITE_VISIT_REQUIRED], true)) {
      $previousStatus = $quote->status;
      $data['status'] = QuoteRequest::STATUS_QUOTE_PREPARED;
      QuoteStatusHistory::create([
        'quote_request_id' => $quote->id,
        'previous_status' => $previousStatus,
        'new_status' => QuoteRequest::STATUS_QUOTE_PREPARED,
        'note' => 'Pricing saved.',
        'changed_by_type' => 'admin',
        'admin_id' => Auth::guard('admin')->id(),
      ]);
    }

    $quote->update($data);

    return back()->with('success', 'Pricing saved.');
  }

  public function sendQuote(Request $request, $id, QuoteAcceptanceService $quotes)
  {
    $quote = QuoteRequest::findOrFail($id);

    if (!$quote->total_price) {
      return back()->with('error', 'Save pricing before sending the quote to the customer.');
    }

    if (in_array($quote->status, [QuoteRequest::STATUS_CUSTOMER_ACCEPTED, QuoteRequest::STATUS_CUSTOMER_DECLINED, QuoteRequest::STATUS_CONVERTED], true)) {
      return back()->with('error', 'This quote has already been responded to and cannot be resent.');
    }

    $token = $quotes->issueToken();
    $previousStatus = $quote->status;

    $quote->forceFill([
      'response_token_hash' => $token['hash'],
      'response_token_expires_at' => $token['expires_at'],
      'response_token_used_at' => null,
      'status' => QuoteRequest::STATUS_QUOTE_SENT,
      'quote_sent_at' => now(),
    ])->save();

    QuoteStatusHistory::create([
      'quote_request_id' => $quote->id,
      'previous_status' => $previousStatus,
      'new_status' => QuoteRequest::STATUS_QUOTE_SENT,
      'note' => 'Quote emailed to customer.',
      'changed_by_type' => 'admin',
      'admin_id' => Auth::guard('admin')->id(),
    ]);

    $this->sendQuoteReadyEmail($quote, $token['plain']);

    return back()->with('success', 'Quote sent to the customer.');
  }

  private function sendQuoteReadyEmail(QuoteRequest $quote, string $plainToken): void
  {
    $template = MailTemplate::where('mail_type', 'quote_ready_customer')->first();
    if (!$template) {
      return;
    }

    $language = Language::where('is_default', 1)->first();
    $serviceName = ServiceContent::where('service_id', $quote->service_id)
      ->where('language_id', optional($language)->id)
      ->value('name') ?? 'Service #' . $quote->service_id;

    $breakdown = '<ul>'
      . '<li>Labour: ' . number_format((float) $quote->labour_cost, 2) . '</li>'
      . '<li>Materials: ' . number_format((float) $quote->material_cost, 2) . '</li>'
      . '<li>Call-out charge: ' . number_format((float) $quote->callout_charge, 2) . '</li>'
      . '<li>Additional charges: ' . number_format((float) $quote->additional_charges, 2) . '</li>'
      . '<li>Discount: -' . number_format((float) $quote->discount, 2) . '</li>'
      . '<li>VAT: ' . number_format((float) $quote->vat_amount, 2) . '</li>'
      . '<li><strong>Total: ' . number_format((float) $quote->total_price, 2) . '</strong></li>'
      . '</ul>'
      . ($quote->customer_notes ? '<p>' . e($quote->customer_notes) . '</p>' : '');

    $acceptLink = route('frontend.quote.response.show', ['token' => $plainToken]);
    $declineLink = route('frontend.quote.response.show', ['token' => $plainToken]) . '?decline=1';

    $body = str_replace(
      ['{customer_name}', '{reference_number}', '{service_title}', '{price_breakdown}', '{expiry_date}', '{accept_link}', '{decline_link}'],
      [
        $quote->customer_name,
        $quote->reference_number,
        $serviceName,
        $breakdown,
        $quote->expiry_date ? $quote->expiry_date->format('d M Y') : 'N/A',
        $acceptLink,
        $declineLink,
      ],
      $template->mail_body
    );

    BasicMailer::sendMail([
      'recipient' => $quote->customer_email,
      'subject' => $template->mail_subject,
      'body' => $body,
    ]);
  }
}
