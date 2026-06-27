<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Booking\ServicePaymentController;
use App\Http\Helpers\AppointmentCompletion;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\UploadFile;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\Services\AppointmentCompletionImage;
use App\Models\Services\Invoice;
use App\Models\Services\ServiceBooking;
use App\Models\Staff\Staff;
use App\Models\Vendor;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PDF;
use Response;
use Throwable;

class AppointmentController extends Controller
{
  public function index(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();

    $orderNumber = $paymentStatus = $orderStatus = $refundStatus = null;
    if ($request->filled('order_no')) {
      $orderNumber = $request['order_no'];
    }
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }
    if ($request->filled('order_status')) {
      $orderStatus = $request['order_status'];
    }
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }
    $information['currencyInfo'] = $this->getCurrencyInfo();

    $information['invoiceVendorName'] = $this->invoiceVendorName();

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->when($orderNumber, function ($query, $orderNumber) {
        return $query->where('order_number', 'like', '%' . $orderNumber . '%');
      })
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->when($orderStatus, function ($query, $orderStatus) {
        return $query->where('order_status', '=', $orderStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->orderByDesc('id')
      ->paginate(10);
    return view('vendors.appointment.all', $information);
  }


  // Display name of the logged-in vendor, used to auto-fill the invoice modal.
  private function invoiceVendorName()
  {
    $vendor = Vendor::join('vendor_infos', 'vendor_infos.vendor_id', '=', 'vendors.id')
      ->where('vendors.id', Auth::guard('vendor')->user()->id)
      ->select('vendors.username', 'vendor_infos.name')
      ->first();

    return $vendor ? ($vendor->name ?? $vendor->username) : '';
  }

  public function pendingAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();

    $paymentStatus = $refundStatus = null;
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }

    $information['invoiceVendorName'] = $this->invoiceVendorName();

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->where('order_status', 'pending')
      ->orderByDesc('id')
      ->paginate(10);
    return view('vendors.appointment.pending', $information);
  }

  public function acceptedAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();

    $paymentStatus = $refundStatus = null;
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }

    $information['invoiceVendorName'] = $this->invoiceVendorName();

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->where('order_status', 'accepted')->orderByDesc('id')
      ->paginate(10);;
    return view('vendors.appointment.accepted', $information);
  }

  public function rejectedAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();

    $refundStatus = $paymentStatus = null;
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }

    $information['invoiceVendorName'] = $this->invoiceVendorName();

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->where('order_status', 'rejected')->orderByDesc('id')
      ->paginate(10);;
    return view('vendors.appointment.rejected', $information);
  }

  public function completedAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $orderNumber = $request->input('order_no');
    $paymentStatus = $request->input('payment_status');
    $information['invoiceVendorName'] = $this->invoiceVendorName();
    $information['booking_item'] = ServiceBooking::with(['completionImages', 'vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->where('vendor_id', Auth::guard('vendor')->id())
      ->when($orderNumber, function ($query, $orderNumber) {
        return $query->where('order_number', 'like', '%' . $orderNumber . '%');
      })
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', $paymentStatus);
      })
      ->where('order_status', 'completed')
      ->orderByDesc('id')
      ->paginate(10);

    return view('vendors.appointment.completed', $information);
  }

  public function show($id)
  {
    $language = Language::where('is_default', 1)->first();
    $language_id = $language->id;

    $appointment = ServiceBooking::with(['completionImages', 'vendorInvoice', 'answers.question.options', 'answers.option', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->findOrFail($id);
    $information['details'] = $appointment;
    $information['appointmentAnswers'] = $appointment->answers;

    $information['vendor_details'] = Vendor::join('vendor_infos', 'vendor_infos.vendor_id', '=', 'vendors.id')
      ->where('vendors.id', $appointment->vendor_id)
      ->first();

    $information['staff'] = Staff::join('staff_contents', 'staff_contents.staff_id', '=', 'staff.id')
      ->where('staff.id', $appointment->staff_id)
      ->select('staff.id', 'staff.email', 'staff.phone', 'staff_contents.location', 'staff_contents.information', 'staff_contents.name')
      ->first();

    return view('vendors.appointment.details', $information);
  }

  public function destroy($id)
  {
    $appointment = ServiceBooking::where('vendor_id', Auth::guard('vendor')->user()->id)->findOrFail($id);
    // delete the attachment
    @unlink(public_path('assets/file/attachments/service/') . $appointment->attachment);
    @unlink(public_path('assets/file/invoices/service/') . $appointment->invoice);
    $appointment->delete();

    return redirect()->back()->with('success', 'Appointment delete successfully!');
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $appointment = ServiceBooking::where('vendor_id', Auth::guard('vendor')->user()->id)->findOrFail($id);
      if (!empty($appointment->invoice)) {
        @unlink(public_path('assets/file/invoices/service/' . $appointment->invoice));
      }
      @unlink(public_path('assets/file/attachments/service/') . $appointment->attachment);
      $appointment->delete();
      $request->session()->flash('success', 'Appointments delete successfully!');
    }
    return Response::json(['status' => 'success'], 200);
  }

  //order status change
  public function updateAppointmentStatus(Request $request, $id)
  {
    $request->validate([
      'order_status' => ['required', Rule::in(['pending', 'accepted', 'rejected'])],
    ]);

    $language = Language::where('is_default', 1)->first();
    $language_id = $language->id;

    $booking = ServiceBooking::with(['serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->findOrFail($id);

    if ($booking->order_status === 'completed') {
      return redirect()->back()->with('warning', 'Completed appointments cannot be changed.');
    }

    if ($booking->order_status === $request['order_status']) {
      return redirect()->back()->with('success', 'Appointment status is already up to date.');
    }

    if ($booking->order_status !== 'pending' && $booking->order_status !== $request['order_status']) {
      return redirect()->back()->with('warning', 'Only pending appointments can be accepted or rejected.');
    }

    $vendor = Vendor::findOrFail($booking->vendor_id);

    if ($request['order_status'] == 'pending') {

      $booking->update([
        'order_status' => 'pending'
      ]);
      return redirect()->back()->with('success', 'Appointment status update successful!');
    } else if ($request['order_status'] == 'accepted') {
      $lessAppointmentNum = intval($vendor->total_appointment) - 1;

      $booking->update([
        'order_status' => 'accepted'
      ]);

      //update less appoitnment number
      $vendor->update([
        'total_appointment' => $lessAppointmentNum,
      ]);

      $purchaseProcess = new ServicePaymentController();
      // generate an invoice in pdf format
      $invoice = $purchaseProcess->generateInvoice($booking);

      // then, update the invoice field info in database
      $booking->update(['invoice' => $invoice]);

      // send a mail to the customer with the invoice for booking accepted
      $purchaseProcess->prepareMail($booking);

      return redirect()->back()->with('success', 'Appointment status update successful!');
    } else if ($request['order_status'] == 'rejected') {
      //after reject
      $lessAppointmentNum = intval($vendor->total_appointment) + 1;
      $booking->update([
        'order_status' => 'rejected',
        'refund' => 'pending',
      ]);

      //update less appoitnment number
      $vendor->update([
        'total_appointment' => $lessAppointmentNum,
      ]);

      // get the mail template info from db
      $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'service_booking_rejected')->first();
      $mailData['subject'] = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;

      // get the website title info from db
      $info = Basic::select('website_title')->first();

      if ($booking->serviceContent->isNotEmpty()) {
        $service =  $booking->serviceContent->first()->name;
      }

      $price = $booking->currency_symbol . $booking->customer_paid;
      $username = $booking->customer_name;
      $websiteTitle = $info->website_title;

      // replacing with actual data

      $mailBody = str_replace('{service_name}', $service, $mailBody);
      $mailBody = str_replace('{username}', $username, $mailBody);
      $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);
      $mailBody = str_replace('{price}', $price, $mailBody);

      $mailData['body'] = $mailBody;
      $mailData['recipient'] = $booking->customer_email;

      BasicMailer::sendMail($mailData);

      return redirect()->back()->with('success', 'Appointment status update successful!');
    }
  }

  public function completeAppointment(Request $request, $id)
  {
    $request->validate([
      'completion_images'   => ['required', 'array', 'min:1', 'max:10'],
      'completion_images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
      'job_certificate'     => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
    ]);

    $vendorId = Auth::guard('vendor')->id();
    $booking = ServiceBooking::with('serviceContent')
      ->where('vendor_id', $vendorId)
      ->findOrFail($id);

    if ($booking->order_status !== 'accepted') {
      return redirect()->back()->with('warning', 'Only accepted appointments can be completed.');
    }

    // Absolute paths of files stored so far — used to roll back on failure.
    $storedFiles = [];

    try {
      DB::transaction(function () use ($request, $booking, $vendorId, &$storedFiles) {
        $lockedBooking = ServiceBooking::where('vendor_id', $vendorId)
          ->lockForUpdate()
          ->findOrFail($booking->id);

        if ($lockedBooking->order_status !== 'accepted') {
          throw new \LogicException('Only accepted appointments can be completed.');
        }

        foreach ($request->file('completion_images') as $image) {
          $fileName = UploadFile::store(public_path('assets/img/appointment-completions/'), $image);
          $storedFiles[] = public_path('assets/img/appointment-completions/' . $fileName);

          AppointmentCompletionImage::create([
            'service_booking_id' => $lockedBooking->id,
            'image'              => $fileName,
            'uploaded_by_type'   => 'vendor',
            'uploaded_by_id'     => $vendorId,
          ]);
        }

        $certFileName = UploadFile::store(public_path('assets/file/certificates/'), $request->file('job_certificate'));
        $storedFiles[] = public_path('assets/file/certificates/' . $certFileName);

        AppointmentCompletion::complete($lockedBooking, 'vendor', $vendorId);

        $lockedBooking->update([
          'job_certificate'   => $certFileName,
        ]);
      });
    } catch (Throwable $exception) {
      foreach ($storedFiles as $path) {
        @unlink($path);
      }

      report($exception);
      return redirect()->back()->with('error', 'Appointment completion failed. Please try again.');
    }

    $freshBooking = $booking->fresh('serviceContent');
    $certPath = $freshBooking->job_certificate
      ? public_path('assets/file/certificates/' . $freshBooking->job_certificate)
      : null;

    AppointmentCompletion::notifyCustomer($freshBooking, $certPath);

    return redirect()->back()->with('success', 'Appointment completed successfully!');
  }
  public function staffAssign(Request $request)
  {
    $ruels = ['staff_id' => 'required'];
    $messages = [
      'staff_id.required' => 'The staff field is required',
    ];
    $validator = Validator::make($request->all(), $ruels, $messages);
    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }
    $appointment = ServiceBooking::findOrFail($request->appointment_id);
    $appointment->update([
      'staff_id' => $request->staff_id,
    ]);

    $request->session()->flash('success', 'New staff assigned successfully!');
    return Response::json(['status' => 'success'], 200);
  }

  // Builds the next sequential invoice number: IV-{2-digit year}{4-digit counter},
  // e.g. IV-260001. The counter resets at the start of each calendar year.
  // Must run inside a DB transaction; the lockForUpdate serialises concurrent
  // submissions so two invoices can't grab the same number.
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

  // Vendor submits a structured invoice for a completed job.
  public function storeInvoice(Request $request, $id)
  {
    $vendorId = Auth::guard('vendor')->id();

    $booking = ServiceBooking::with('serviceContent')
      ->where('vendor_id', $vendorId)
      ->findOrFail($id);

    if ($booking->order_status !== 'completed') {
      return redirect()->back()->with('warning', 'You can only upload an invoice for a completed job.');
    }

    if ($booking->vendorInvoice) {
      return redirect()->back()->with('warning', 'An invoice has already been submitted for this job.');
    }

    $request->validate([
      'invoice_date'   => ['required', 'date'],
      'amount'         => ['required', 'numeric', 'min:0'],
      'vat'            => ['nullable', 'numeric', 'min:0'],
      'invoice_file'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
      'bank_details'   => ['nullable', 'string', 'max:2000'],
      'notes'          => ['nullable', 'string', 'max:2000'],
    ]);

    $customerPaidAmount = round((float) $booking->customer_paid, 2);
    $commissionPercentage = 20;
    $commissionAmount = round(($customerPaidAmount * $commissionPercentage) / 100, 2);
    $vendorPayoutAmount = round($customerPaidAmount - $commissionAmount, 2);
    $vat   = $request->filled('vat') ? (float) $request->vat : 0;
    $total = $customerPaidAmount + $vat;

    $serviceTitle = $booking->serviceContent->isNotEmpty()
      ? $booking->serviceContent->first()->name
      : null;

    $vendor = Vendor::join('vendor_infos', 'vendor_infos.vendor_id', '=', 'vendors.id')
      ->where('vendors.id', $vendorId)
      ->select('vendors.username', 'vendor_infos.name')
      ->first();
    $vendorName = $vendor ? ($vendor->name ?? $vendor->username) : null;

    $fileName = $request->hasFile('invoice_file')
      ? UploadFile::store(public_path('assets/file/invoices/vendor/'), $request->file('invoice_file'))
      : null;

    $invoice = DB::transaction(function () use (
      $booking, $vendorId, $request, $serviceTitle, $vendorName, $customerPaidAmount,
      $vat, $total, $commissionPercentage, $commissionAmount, $vendorPayoutAmount, $fileName
    ) {
      return Invoice::create([
        'service_booking_id' => $booking->id,
        'vendor_id'          => $vendorId,
        'invoice_number'     => $this->generateInvoiceNumber(),
        'invoice_date'       => $request->invoice_date,
        'service_title'      => $serviceTitle,
        'vendor_name'        => $vendorName,
        'amount'             => $customerPaidAmount,
        'vat'                => $request->filled('vat') ? $request->vat : null,
        'total_amount'       => $total,
        'customer_paid_amount' => $customerPaidAmount,
        'commission_percentage' => $commissionPercentage,
        'commission_amount'  => $commissionAmount,
        'vendor_payout_amount' => $vendorPayoutAmount,
        'invoice_file'       => $fileName,
        'bank_details'       => $request->bank_details,
        'notes'              => $request->notes,
        'invoice_status'     => 'submitted',
        'payment_status'     => 'unpaid',
      ]);
    });

    $customerInvoiceFile = $this->generateCustomerInvoicePdf($booking, $invoice);
    $invoice->update(['customer_invoice_file' => $customerInvoiceFile]);

    $this->sendVendorInvoiceSubmittedMail($booking, $invoice);

    return redirect()->back()->with('success', 'Invoice submitted successfully!');
  }

  private function generateCustomerInvoicePdf(ServiceBooking $booking, Invoice $invoice): string
  {
    $vendor = Vendor::with('activeBankAccount')->find($invoice->vendor_id);
    $vendorName = $invoice->vendor_name ?: optional($vendor)->username ?: 'Service provider';
    $fileName = 'vendor-invoice-' . $invoice->id . '-' . $booking->order_number . '.pdf';
    $directory = public_path('assets/file/invoices/service/');
    @mkdir($directory, 0775, true);

    PDF::loadView('frontend.services.vendor-customer-invoice', [
      'booking' => $booking,
      'invoice' => $invoice,
      'issuerName' => $vendorName,
      'issuerEmail' => optional($vendor)->email,
      'issuerPhone' => optional($vendor)->phone,
      'bankAccount' => optional($vendor)->activeBankAccount,
    ])->save($directory . $fileName);

    return $fileName;
  }

  private function sendVendorInvoiceSubmittedMail(ServiceBooking $booking, Invoice $invoice): void
  {
    if (empty($booking->customer_email)) {
      return;
    }

    $vendor = Vendor::find($invoice->vendor_id);
    $customerName = $booking->customer_name ?: 'Customer';
    $serviceTitle = $invoice->service_title ?: 'Service appointment';
    $vendorName = $invoice->vendor_name ?: optional($vendor)->username ?: 'Service provider';
    $vendorEmail = optional($vendor)->email ?? '';
    $vendorPhone = optional($vendor)->phone ?? '';
    $currency = $booking->currency_symbol ?: $booking->currency_text;
    $amount = $this->formatInvoiceAmount($invoice->amount, $currency);
    $vat = $invoice->vat !== null ? $this->formatInvoiceAmount($invoice->vat, $currency) : null;
    $total = $this->formatInvoiceAmount($invoice->total_amount, $currency);
    $invoiceDate = $invoice->invoice_date
      ? $invoice->invoice_date->format('M d, Y')
      : '-';
    $invoiceFileUrl = $invoice->customer_invoice_file
      ? asset('assets/file/invoices/service/' . $invoice->customer_invoice_file)
      : null;

    $contactRows = '';
    if (!empty($vendorEmail)) {
      $contactRows .= '<p><strong>Email:</strong> ' . e($vendorEmail) . '</p>';
    }
    if (!empty($vendorPhone)) {
      $contactRows .= '<p><strong>Phone:</strong> ' . e($vendorPhone) . '</p>';
    }

    $mailData = [
      'recipient' => $booking->customer_email,
      'subject' => 'Invoice submitted for booking #' . $booking->order_number,
      'body' => '<p>Hello ' . e($customerName) . ',</p>'
        . '<p>An invoice has been submitted for your completed booking. The invoice details are listed below.</p>'
        . '<table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 640px;">'
        . '<tr><th align="left">Invoice Number</th><td>' . e($invoice->invoice_number) . '</td></tr>'
        . '<tr><th align="left">Invoice Date</th><td>' . e($invoiceDate) . '</td></tr>'
        . '<tr><th align="left">Booking Number</th><td>#' . e($booking->order_number) . '</td></tr>'
        . '<tr><th align="left">Service</th><td>' . e($serviceTitle) . '</td></tr>'
        . '<tr><th align="left">Vendor</th><td>' . e($vendorName) . '</td></tr>'
        . '<tr><th align="left">Amount</th><td>' . e($amount) . '</td></tr>'
        . ($vat !== null ? '<tr><th align="left">VAT / Tax</th><td>' . e($vat) . '</td></tr>' : '')
        . '<tr><th align="left">Total</th><td><strong>' . e($total) . '</strong></td></tr>'
        . '</table>'
        . ($invoiceFileUrl ? '<p><a href="' . e($invoiceFileUrl) . '" target="_blank">Download Invoice</a></p>' : '')
        . '<p>For invoice or payment questions, please contact 11Done Support Team using the contact details below.</p>'
        . '<p><strong>Email:</strong> info@11done.com</p>'
    ];

    BasicMailer::sendMail($mailData);
  }

  private function formatInvoiceAmount($amount, ?string $currency): string
  {
    $formatted = number_format((float) $amount, 2);

    return $currency ? trim($currency . ' ' . $formatted) : $formatted;
  }
}
