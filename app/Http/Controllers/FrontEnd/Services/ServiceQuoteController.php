<?php

namespace App\Http\Controllers\FrontEnd\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\UploadFile;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\QuoteQuestion;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestAnswer;
use App\Models\QuoteRequestAttachment;
use App\Models\Services\ServiceContent;
use App\Models\Services\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Deliberately separate from ServiceCartController — a quote request never
 * touches the cart/session pricing state, it only gathers information and
 * creates a QuoteRequest record for admin to price.
 */
class ServiceQuoteController extends Controller
{
  /** A quote service must be active and explicitly service_type='quote'. */
  private function quoteService(int $serviceId): ?Services
  {
    return Services::where('id', $serviceId)
      ->where('status', 1)
      ->where('service_type', 'quote')
      ->first();
  }

  public function questions(int $serviceId)
  {
    $service = $this->quoteService($serviceId);
    abort_unless($service, 404);

    $language = (new MiscellaneousController())->getLanguage();
    $serviceName = ServiceContent::where('service_id', $serviceId)
      ->where('language_id', $language->id)
      ->value('name') ?? 'Service #' . $serviceId;

    $questions = QuoteQuestion::where('service_id', $serviceId)
      ->with('options')
      ->orderBy('sort_order')
      ->get();

    $user = Auth::guard('web')->user();

    $queryResult = [
      'pageHeading' => (new MiscellaneousController())->getPageHeading($language),
      'bgImg' => MiscellaneousController::getBreadcrumb(),
      'service' => $service,
      'serviceId' => $serviceId,
      'serviceName' => $serviceName,
      'questions' => $questions,
      'prefill' => [
        'name' => old('name', $user->name ?? ''),
        'email' => old('email', $user->email ?? ''),
        'phone' => old('phone', $user->phone ?? ''),
      ],
    ];

    return view('frontend.services.quote.questions', $queryResult);
  }

  public function submit(Request $request, int $serviceId)
  {
    $service = $this->quoteService($serviceId);
    abort_unless($service, 404);

    $questions = QuoteQuestion::where('service_id', $serviceId)->orderBy('sort_order')->get();

    $rules = [
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'phone' => 'required|string|max:30',
      'postcode' => 'required|string|max:12',
      'address' => 'required|string|max:1000',
      'preferred_contact_method' => 'nullable|string|max:50',
      'preferred_date' => 'nullable|date',
      'description' => 'nullable|string|max:5000',
      'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
    ];

    foreach ($questions as $question) {
      $key = 'answers.' . $question->id;
      if ($question->isUploadType()) {
        $rules['question_files.' . $question->id] = [
          $question->is_required ? 'required' : 'nullable',
          'file',
          'mimes:jpg,jpeg,png,pdf,doc,docx',
          'max:10240',
        ];
        continue;
      }
      $rules[$key] = $question->is_required ? 'required' : 'nullable';
      if ($question->type === 'multi_select') {
        $rules[$key] = ($question->is_required ? 'required' : 'nullable') . '|array';
      }
    }

    $request->validate($rules);

    $referenceNumber = $this->generateReferenceNumber();
    $user = Auth::guard('web')->user();

    $quote = DB::transaction(function () use ($request, $service, $questions, $referenceNumber, $user) {
      $quote = QuoteRequest::create([
        'reference_number' => $referenceNumber,
        'service_id' => $service->id,
        'vendor_id' => $service->vendor_id ?: 0,
        'user_id' => $user->id ?? null,
        'customer_name' => $request->input('name'),
        'customer_phone' => $request->input('phone'),
        'customer_email' => $request->input('email'),
        'customer_postcode' => $request->input('postcode'),
        'customer_address' => $request->input('address'),
        'preferred_contact_method' => $request->input('preferred_contact_method'),
        'preferred_date' => $request->input('preferred_date') ?: null,
        'description' => $request->input('description'),
        'status' => QuoteRequest::STATUS_NEW,
      ]);

      foreach ($questions as $question) {
        if ($question->isUploadType()) {
          if ($request->hasFile('question_files.' . $question->id)) {
            $this->storeAttachment($quote, $request->file('question_files.' . $question->id), $question->id);
          }
          continue;
        }

        $rawAnswer = $request->input('answers.' . $question->id);
        $answerText = $this->resolveAnswerText($question, $rawAnswer);

        if ($answerText === null || $answerText === '') {
          continue;
        }

        QuoteRequestAnswer::create([
          'quote_request_id' => $quote->id,
          'quote_question_id' => $question->id,
          'answer' => $answerText,
        ]);
      }

      // General end-of-form attachments (not tied to a specific question).
      foreach ($request->file('attachments', []) as $file) {
        if ($file) {
          $this->storeAttachment($quote, $file, null);
        }
      }

      return $quote;
    });

    $this->sendSubmissionEmails($quote);

    return redirect()->route('frontend.service_quote.thank_you', ['reference' => $quote->reference_number]);
  }

  public function thankYou(string $reference)
  {
    // Deliberately does not expose Q&A/pricing here — a reference number is
    // not treated as a secret. Viewing/accepting a quote is gated by the
    // token-based link emailed once admin has priced it, not this page.
    $quote = QuoteRequest::where('reference_number', $reference)->first();
    abort_unless($quote, 404);

    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();

    return view('frontend.services.quote.thank-you', [
      'pageHeading' => $misc->getPageHeading($language),
      'bgImg' => MiscellaneousController::getBreadcrumb(),
      'reference' => $quote->reference_number,
    ]);
  }

  private function resolveAnswerText(QuoteQuestion $question, $rawAnswer): ?string
  {
    if ($rawAnswer === null) {
      return null;
    }

    if (in_array($question->type, ['single_select', 'dropdown'], true)) {
      $option = $question->options->firstWhere('id', (int) $rawAnswer);
      return $option ? $option->option_text : null;
    }

    if ($question->type === 'multi_select') {
      $ids = is_array($rawAnswer) ? $rawAnswer : [$rawAnswer];
      return $question->options->whereIn('id', array_map('intval', $ids))
        ->pluck('option_text')
        ->implode(', ');
    }

    if ($question->type === 'yes_no') {
      return $rawAnswer ? 'Yes' : 'No';
    }

    return trim((string) $rawAnswer);
  }

  private function storeAttachment(QuoteRequest $quote, $file, ?int $questionId): void
  {
    $fileName = UploadFile::store(public_path(QuoteRequestAttachment::STORAGE_DIR), $file);
    $isImage = in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'], true);

    QuoteRequestAttachment::create([
      'quote_request_id' => $quote->id,
      'quote_question_id' => $questionId,
      'file_name' => $fileName,
      'original_name' => $file->getClientOriginalName(),
      'file_type' => $isImage ? QuoteRequestAttachment::TYPE_IMAGE : QuoteRequestAttachment::TYPE_DOCUMENT,
      'uploaded_by_type' => 'customer',
      'uploaded_by_id' => Auth::guard('web')->id(),
    ]);
  }

  private function generateReferenceNumber(): string
  {
    do {
      $candidate = 'QR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    } while (QuoteRequest::where('reference_number', $candidate)->exists());

    return $candidate;
  }

  private function sendSubmissionEmails(QuoteRequest $quote): void
  {
    $language = Language::where('is_default', 1)->first();
    $serviceName = ServiceContent::where('service_id', $quote->service_id)
      ->where('language_id', optional($language)->id)
      ->value('name') ?? 'Service #' . $quote->service_id;
    $websiteTitle = optional(Basic::select('website_title')->first())->website_title ?? '';

    $answersSummary = '<ul>' . $quote->answers()
      ->with('question')
      ->get()
      ->map(function (QuoteRequestAnswer $answer) {
        $label = optional($answer->question)->summary_label ?: optional($answer->question)->title ?: 'Answer';
        return '<li><strong>' . e($label) . ':</strong> ' . e($answer->answer) . '</li>';
      })
      ->implode('') . '</ul>';

    $customerTemplate = MailTemplate::where('mail_type', 'quote_request_submitted_customer')->first();
    if ($customerTemplate) {
      $body = str_replace(
        ['{customer_name}', '{reference_number}', '{service_title}', '{questions_answers_summary}', '{website_title}'],
        [$quote->customer_name, $quote->reference_number, $serviceName, $answersSummary, $websiteTitle],
        $customerTemplate->mail_body
      );
      BasicMailer::sendMail([
        'recipient' => $quote->customer_email,
        'subject' => $customerTemplate->mail_subject,
        'body' => $body,
      ]);
    }

    $adminEmail = optional(Basic::select('email_address')->first())->email_address;
    $adminTemplate = MailTemplate::where('mail_type', 'quote_request_submitted_admin')->first();
    if ($adminTemplate && $adminEmail) {
      $adminLink = route('admin.quote_requests.show', $quote->id);
      $body = str_replace(
        ['{reference_number}', '{service_title}', '{customer_name}', '{customer_email}', '{customer_phone}', '{quote_admin_link}'],
        [$quote->reference_number, $serviceName, $quote->customer_name, $quote->customer_email, $quote->customer_phone, $adminLink],
        $adminTemplate->mail_body
      );
      BasicMailer::sendMail([
        'recipient' => $adminEmail,
        'subject' => $adminTemplate->mail_subject,
        'body' => $body,
      ]);
    }
  }
}
