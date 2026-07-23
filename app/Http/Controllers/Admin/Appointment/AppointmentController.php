<?php

namespace App\Http\Controllers\Admin\Appointment;

use App\Http\Controllers\Admin\Transaction\TransactionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Booking\ServicePaymentController;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Helpers\AdminComplianceTasks;
use App\Http\Helpers\AppointmentCompletion;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\VendorAvailability;
use App\Http\Helpers\VendorMatching;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServiceReview;
use App\Models\Services\Services;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffGlobalHour;
use App\Models\Staff\StaffServiceHour;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Response;

class AppointmentController extends Controller
{

  public function index(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $information['vendors'] = $this->getActiveVendors($language_id);

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
    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
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
    $information['vendorBusyMap'] = VendorAvailability::busyMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    $information['vendorEligibleMap'] = VendorMatching::eligibleMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    return view('admin.appointment.all', $information);
  }

  public function createManualJob(Request $request)
  {
    $language = Language::where('code', request()->language)->first() ?: Language::where('is_default', 1)->firstOrFail();
    $languageId = $language->id;

    $information['langs'] = Language::all();
    $information['currencyInfo'] = $this->getCurrencyInfo();
    $information['services'] = ServiceContent::query()
      ->with('service')
      ->where('language_id', $languageId)
      ->orderBy('name')
      ->get()
      ->unique('service_id')
      ->values();
    $information['staffMembers'] = Staff::with('StaffContent')->orderByDesc('id')->get();
    $information['staffHours'] = StaffServiceHour::orderBy('start_time')->get();
    $information['globalHours'] = StaffGlobalHour::orderBy('start_time')->get();
    $information['questionsByService'] = Question::with('options')
      ->whereNotNull('service_id')
      ->orderBy('service_id')
      ->orderBy('id')
      ->get()
      ->groupBy('service_id');
    $information['optionsByQuestion'] = QuestionOption::orderBy('id')->get()->groupBy('question_id');

    return view('admin.appointment.manual-create', $information);
  }

  public function manualJobPrice(Request $request)
  {
    $data = $request->validate([
      'service_id' => ['required', 'integer', 'exists:services,id'],
      'postcode' => ['nullable', 'string', 'max:100'],
      'question_answers' => ['nullable', 'array'],
      'question_answers.*' => ['nullable'],
    ]);

    $service = Services::findOrFail($data['service_id']);
    $answers = $this->normalizeManualQuestionAnswers($data['question_answers'] ?? [], (int) $service->id);
    $pricing = app(ServicePaymentController::class)->resolveServiceCartItemPrice(
      $service,
      $answers,
      $data['postcode'] ?? null
    );

    return response()->json([
      'success' => true,
      'price' => round((float) ($pricing['total'] ?? 0), 2),
      'base_price' => round((float) ($pricing['base'] ?? 0), 2),
      'question_surcharge' => round((float) ($pricing['questions'] ?? 0), 2),
      'postcode_surcharge' => round((float) ($pricing['postcode_surcharge'] ?? 0), 2),
      'price_display' => symbolPrice(round((float) ($pricing['total'] ?? 0), 2)),
    ]);
  }

  public function storeManualJob(Request $request)
  {
    $data = $request->validate([
      'service_id' => ['required', 'integer', 'exists:services,id'],
      'customer_paid' => ['required', 'numeric', 'min:0'],
      'customer_name' => ['required', 'string', 'max:255'],
      'customer_phone' => ['required', 'string', 'max:255'],
      'customer_email' => ['required', 'email', 'max:255'],
      'customer_address' => ['required', 'string', 'max:500'],
      'customer_zip_code' => ['required', 'string', 'max:100'],
      'customer_country' => ['nullable', 'string', 'max:255'],
      'booking_date' => ['required', 'date'],
      'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
      'service_hour_id' => ['nullable', 'integer'],
      'start_date' => ['nullable', 'date_format:H:i'],
      'end_date' => ['nullable', 'date_format:H:i'],
      'payment_status' => ['required', Rule::in(['pending', 'completed', 'rejected'])],
      'order_status' => ['required', Rule::in(['pending', 'accepted', 'completed', 'rejected'])],
      'access_notes' => ['nullable', 'string', 'max:2000'],
      'question_answers' => ['nullable', 'array'],
      'question_answers.*' => ['nullable'],
      'send_customer_email' => ['nullable', 'boolean'],
    ]);

    $service = Services::findOrFail($data['service_id']);
    $staffId = $data['staff_id'] ?? null;
    $serviceHourId = $data['service_hour_id'] ?? null;
    $startTime = $data['start_date'] ?? null;
    $endTime = $data['end_date'] ?? null;

    if ($serviceHourId) {
      $hour = $staffId ? StaffServiceHour::find($serviceHourId) : null;
      $hour = $hour ?: StaffGlobalHour::find($serviceHourId);

      if (!$hour) {
        return redirect()->back()->withInput()->with('warning', __('Selected time slot was not found.'));
      }

      $startTime = $hour->start_time;
      $endTime = $hour->end_time;
    }

    if (empty($startTime) || empty($endTime)) {
      return redirect()->back()->withInput()->with('warning', __('Please select an existing time slot or enter both start and end time.'));
    }

    $currencyInfo = $this->getCurrencyInfo();
    $questionAnswers = $this->normalizeManualQuestionAnswers($data['question_answers'] ?? [], (int) $service->id);
    $paymentProcess = new ServicePaymentController();
    $pricing = $paymentProcess->resolveServiceCartItemPrice(
      $service,
      $questionAnswers,
      $data['customer_zip_code'] ?? null
    );
    $calculatedCustomerPaid = round((float) ($pricing['total'] ?? $data['customer_paid']), 2);

    session()->forget('multiServiceOrder');

    $arrData = [
      'item_type' => 'service',
      'zoom_status' => $service->zoom_meeting == 1 ? 1 : 0,
      'calender_status' => $service->calendar_status == 1 ? 1 : 0,
      'customer_name' => $data['customer_name'],
      'customer_phone' => $data['customer_phone'],
      'customer_email' => $data['customer_email'],
      'customer_address' => $data['customer_address'],
      'customer_zip_code' => $data['customer_zip_code'],
      'customer_country' => $data['customer_country'] ?? 'United Kingdom',
      'start_date' => $startTime,
      'end_date' => $endTime,
      'booking_date' => $data['booking_date'],
      'staff_id' => $staffId,
      'service_id' => $service->id,
      'service_package_id' => null,
      'package_name' => null,
      'package_price' => null,
      'package_included_services' => null,
      'max_person' => 1,
      'user_id' => null,
      'vendor_id' => (int) ($service->vendor_id ?? 0),
      'service_hour_id' => $serviceHourId,
      'customer_paid' => $calculatedCustomerPaid,
      'urgency_surcharge_amount' => null,
      'urgency_surcharge_period' => null,
      'billing_same' => 1,
      'site_name' => null,
      'site_phone' => null,
      'site_email' => null,
      'site_address' => null,
      'site_zip_code' => null,
      'site_country' => null,
      'access_notes' => $data['access_notes'] ?? null,
      'currencyText' => $currencyInfo->base_currency_text,
      'currencyTextPosition' => $currencyInfo->base_currency_text_position,
      'currencySymbol' => $currencyInfo->base_currency_symbol,
      'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
      'paymentMethod' => 'Manual Admin',
      'gatewayType' => 'offline',
      'paymentStatus' => $data['payment_status'],
      'orderStatus' => $data['order_status'],
      'refund' => 'pending',
      'attachment' => null,
      'group_token' => null,
      'question_answers' => json_encode($questionAnswers),
    ];

    $booking = $paymentProcess->finalizeOrder($arrData)->first();

    if ($request->boolean('send_customer_email')) {
      try {
        if ($data['payment_status'] === 'completed') {
          $mailType = 'service_payment_approved';
        } elseif ($data['payment_status'] === 'rejected') {
          $mailType = 'service_payment_rejected';
        } else {
          $mailType = 'service_payment_pending';
        }

        payemntStatusMail($mailType, $booking->id);
      } catch (\Throwable $exception) {
        return redirect()
          ->route('admin.appointment.details', ['id' => $booking->id, 'language' => request()->language])
          ->with('warning', __('Manual job created, but the customer email could not be sent: ') . $exception->getMessage());
      }
    }

    return redirect()
      ->route('admin.appointment.details', ['id' => $booking->id, 'language' => request()->language])
      ->with('success', __('Manual job created successfully.'));
  }

  private function normalizeManualQuestionAnswers(array $answers, int $serviceId): array
  {
    $questionIds = Question::where('service_id', $serviceId)->pluck('id')->map(function ($id) {
      return (string) $id;
    })->all();
    $allowedQuestionIds = array_flip($questionIds);
    $normalized = [];

    foreach ($answers as $questionId => $answer) {
      $questionKey = (string) $questionId;
      if (!isset($allowedQuestionIds[$questionKey])) {
        continue;
      }

      if (is_array($answer)) {
        $answer = array_values(array_filter($answer, function ($value) {
          return $value !== null && $value !== '';
        }));
        if (count($answer) === 0) {
          continue;
        }
      } elseif ($answer === null || $answer === '') {
        continue;
      }

      $normalized[$questionKey] = $answer;
    }

    return $normalized;
  }

  /**
   * Availability compliance report: every upcoming, active job whose ASSIGNED vendor
   * has a conflict for that slot — a double-booking, self-marked unavailability
   * (date exception) or a weekly-schedule clash. These are conflicts the admin
   * needs to resolve (e.g. reassign the vendor).
   */
  public function availabilityCompliance(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $information['currencyInfo'] = $this->getCurrencyInfo();

    // Availability conflicts (assigned vendor unavailable for the slot).
    $conflictMap = VendorAvailability::conflictMap();
    $information['conflictBookings'] = ServiceBooking::with(['vendor', 'vendorInfo', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->whereIn('id', array_keys($conflictMap))
      ->orderBy('booking_date')
      ->get();
    $information['complianceReasons'] = $conflictMap;

    // Other admin compliance tasks.
    $information['pendingDocumentApprovals'] = AdminComplianceTasks::pendingDocumentApprovals();
    $information['expiredDocuments'] = AdminComplianceTasks::expiredDocuments();
    $information['pendingPayments'] = AdminComplianceTasks::pendingPayments($language_id);
    $information['jobsNeedingVendor'] = AdminComplianceTasks::jobsNeedingVendor($language_id);
    $information['taskCounts'] = AdminComplianceTasks::counts();

    return view('admin.appointment.availability-compliance', $information);
  }

  /**
   * Customer Compliance: pending customer-initiated date/time change requests
   * that an admin needs to approve or reject.
   */
  public function customerCompliance(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();

    $information['requests'] = ServiceBooking::with(['vendor', 'vendorInfo', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->where('reschedule_status', 'requested')
      ->orderBy('updated_at', 'desc')
      ->get();

    return view('admin.appointment.customer-compliance', $information);
  }

  public function pendingAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $information['vendors'] = $this->getActiveVendors($language_id);

    $paymentStatus = $refundStatus = null;
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->where('order_status', 'pending')
      ->orderByDesc('id')
      ->paginate(10);
    $information['vendorBusyMap'] = VendorAvailability::busyMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    $information['vendorEligibleMap'] = VendorMatching::eligibleMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    return view('admin.appointment.pending', $information);
  }

  public function acceptedAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $information['vendors'] = $this->getActiveVendors($language_id);

    $paymentStatus = $refundStatus = null;
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->where('order_status', 'accepted')->orderByDesc('id')
      ->paginate(10);
    $information['vendorBusyMap'] = VendorAvailability::busyMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    $information['vendorEligibleMap'] = VendorMatching::eligibleMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    return view('admin.appointment.accepted', $information);
  }

  public function rejectedAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $information['vendors'] = $this->getActiveVendors($language_id);

    $refundStatus = $paymentStatus = null;
    if ($request->filled('refund')) {
      $refundStatus = $request['refund'];
    }
    if ($request->filled('payment_status')) {
      $paymentStatus = $request['payment_status'];
    }

    $information['booking_item'] = ServiceBooking::with(['vendorInfo', 'vendorInvoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', '=', $refundStatus);
      })
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', '=', $paymentStatus);
      })
      ->where('order_status', 'rejected')->orderByDesc('id')
      ->paginate(10);;
    $information['vendorBusyMap'] = VendorAvailability::busyMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    $information['vendorEligibleMap'] = VendorMatching::eligibleMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    return view('admin.appointment.rejected', $information);
  }

  public function completedAppointment(Request $request)
  {
    $language = Language::where('code', request()->language)->firstOrFail();
    $language_id = $language->id;
    $information['langs'] = Language::all();
    $information['vendors'] = $this->getActiveVendors($language_id);
    $orderNumber = $request->input('order_no');
    $paymentStatus = $request->input('payment_status');
    $refundStatus = $request->input('refund');
    $information['booking_item'] = ServiceBooking::with(['completionImages', 'vendorInfo', 'invoice', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->when($orderNumber, function ($query, $orderNumber) {
        return $query->where('order_number', 'like', '%' . $orderNumber . '%');
      })
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', $paymentStatus);
      })
      ->when($refundStatus, function ($query, $refundStatus) {
        return $query->where('refund', $refundStatus);
      })
      ->where('order_status', 'completed')
      ->orderByDesc('id')
      ->paginate(10);

    $information['vendorBusyMap'] = VendorAvailability::busyMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    $information['vendorEligibleMap'] = VendorMatching::eligibleMapForAppointments($information['booking_item']->getCollection(), $information['vendors']);
    return view('admin.appointment.completed', $information);
  }

  public function show($id)
  {
    $language = Language::where('is_default', 1)->first();
    $language_id = $language->id;

    $appointment = ServiceBooking::with(['completionImages', 'vendorInvoice', 'answers.question.options', 'answers.option', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->findOrFail($id);

    $information['details'] = $appointment;
    $information['appointmentAnswers'] = $appointment->answers;

    $information['staff'] = Staff::join('staff_contents', 'staff_contents.staff_id', '=', 'staff.id')
      ->where('staff.id', $appointment->staff_id)
      ->select('staff.id', 'staff.email', 'staff.phone', 'staff_contents.location', 'staff_contents.information', 'staff_contents.name')
      ->first();


    if ($appointment->vendor_id != 0) {
      $information['vendor_details'] = Vendor::with(['vendor_infos' => function ($q) use ($language_id) {
        $q->where('language_id', $language_id);
      }])
        ->where('vendors.id', $appointment->vendor_id)
        ->first();
    } else {
      $information['vendor_details'] = Admin::whereNull('role_id')->first();
    }

    $information['vendors'] = $this->getActiveVendors($language_id);
    $information['busyVendorIds'] = VendorAvailability::busyVendorIdsFor($appointment, $information['vendors']);
    $information['eligibleVendorIds'] = VendorMatching::eligibleVendorIdsFor($appointment, $information['vendors']);

    return view('admin.appointment.details', $information);
  }

  // Vendor calendar overview page (all vendors' appointments).
  public function calendar()
  {
    $language = Language::where('code', request()->language)->first()
      ?? Language::where('is_default', 1)->first();
    $information['langs'] = Language::all();
    $information['vendors'] = $this->getActiveVendors($language->id);

    return view('admin.appointment.calendar', $information);
  }

  // JSON feed for the FullCalendar widget on the calendar page.
  public function calendarEvents(Request $request)
  {
    $language = Language::where('is_default', 1)->first();

    $bookings = ServiceBooking::with(['serviceContent' => function ($q) use ($language) {
      $q->where('language_id', $language->id);
    }])
      ->where('order_status', '!=', 'rejected')
      ->when($request->filled('vendor_id'), function ($query) use ($request) {
        return $query->where('vendor_id', (int) $request->vendor_id);
      })
      ->when($request->filled('start') && $request->filled('end'), function ($query) use ($request) {
        return $query->whereBetween('booking_date', [
          \Carbon\Carbon::parse($request->start)->toDateString(),
          \Carbon\Carbon::parse($request->end)->toDateString(),
        ]);
      })
      ->get();

    $vendorNames = Vendor::with(['vendor_infos' => function ($q) use ($language) {
      $q->where('language_id', $language->id);
    }])->get()->keyBy('id');

    $events = $bookings->map(function (ServiceBooking $booking) use ($vendorNames) {
      $serviceName = ($booking->item_type ?? 'service') === 'package'
        ? ($booking->package_name ?? 'Service Package')
        : (optional($booking->serviceContent->first())->name ?? ('Service #' . $booking->service_id));
      $vendorId = (int) $booking->vendor_id;
      $vendorName = $vendorId === 0
        ? 'Admin'
        : (optional(optional($vendorNames->get($vendorId))->vendor_infos->first())->name
          ?? optional($vendorNames->get($vendorId))->username
          ?? 'Vendor');

      [$start, $end, $allDay] = $this->calendarEventTimes($booking);

      return [
        'id' => $booking->id,
        'title' => $serviceName . ' (#' . $booking->order_number . ')',
        'start' => $start,
        'end' => $end,
        'allDay' => $allDay,
        'url' => route('admin.appointment.details', ['id' => $booking->id]),
        'color' => $this->vendorColor($vendorId),
        'extendedProps' => [
          'vendor' => $vendorName,
          'status' => ucfirst($booking->order_status),
          'customer' => $booking->customer_name,
        ],
      ];
    })->values();

    return Response::json($events);
  }

  private function calendarEventTimes(ServiceBooking $booking): array
  {
    $date = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->toDateString() : null;

    if (!$date) {
      return [null, null, true];
    }

    try {
      if ($booking->start_date && $booking->end_date) {
        $start = $date . 'T' . \Carbon\Carbon::parse($booking->start_date)->format('H:i:s');
        $end = $date . 'T' . \Carbon\Carbon::parse($booking->end_date)->format('H:i:s');

        return [$start, $end, false];
      }
    } catch (\Exception $exception) {
      // fall through to all-day
    }

    return [$date, null, true];
  }

  // Deterministic colour per vendor so events read consistently across reloads.
  private function vendorColor(int $vendorId): string
  {
    if ($vendorId === 0) {
      return '#6c757d';
    }

    $palette = ['#4e73df', '#1cc88a', '#e74a3b', '#f6c23e', '#36b9cc', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#17a2b8'];

    return $palette[$vendorId % count($palette)];
  }

  public function updatePaymentStatus(Request $request, $id)
  {
    $language = Language::where('is_default', 1)->first();
    $language_id = $language->id;
    $appointment = ServiceBooking::with(['serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->findOrFail($id);

    if ($request['payment_status'] == 'pending') {

      $appointment->update([
        'payment_status' => 'pending'
      ]);
      //send mail
      $type = 'service_payment_pending';
      payemntStatusMail($type, $id);

      return redirect()->back()->with('success', 'Payment status update successful!');
    } else if ($request['payment_status'] == 'completed') {
      $appointment->update([
        'payment_status' => 'completed'
      ]);

      //send mail
      $type = 'service_payment_approved';
      payemntStatusMail($type, $id);

      $arrData = array(
        'customer_paid' => $appointment->customer_paid,
        'paymentMethod' => $appointment->payment_method,
        'currencySymbol' => $appointment->currency_symbol,
        'currencySymbolPosition' => $appointment->currency_symbol_position,
        'paymentStatus' => $appointment->payment_status,
        'vendor_id' => $appointment->vendor_id,
      );

      $transaction = new TransactionController();
      $transaction->storeTransaction($arrData);

      return redirect()->back()->with('success', 'Payment status update successful!');
    } else {
      //after reject
      $appointment->update([
        'payment_status' => 'rejected'
      ]);

      //send mail
      $type = 'service_payment_rejected';
      payemntStatusMail($type, $id);

      return redirect()->back();
    }
  }

  //order status change
  public function updateAppointmentStatus(Request $request, $id)
  {
    $request->validate([
      'order_status' => ['required', Rule::in(['pending', 'accepted', 'completed', 'rejected'])],
    ]);

    $booking = ServiceBooking::findOrFail($id);

    if ($booking->order_status === 'completed') {
      return redirect()->back()->with('warning', 'Completed appointments cannot be changed.');
    }

    if ($request['order_status'] === 'completed') {
      if ($booking->order_status !== 'accepted') {
        return redirect()->back()->with('warning', 'Only accepted appointments can be completed.');
      }

      AppointmentCompletion::complete($booking, 'admin', Auth::guard('admin')->id());
      AppointmentCompletion::notifyCustomer($booking);

      return redirect()->back()->with('success', 'Appointment completed successfully!');
    }

    if ($booking->order_status === $request['order_status']) {
      return redirect()->back()->with('success', 'Appointment status is already up to date.');
    }

    if ($booking->order_status !== 'pending' && $booking->order_status !== $request['order_status']) {
      return redirect()->back()->with('warning', 'Only pending appointments can be accepted or rejected.');
    }

    if ($booking->vendor_id != 0) {
      $vendor = Vendor::findOrFail($booking->vendor_id);
    }
    if ($request['order_status'] == 'pending') {

      $booking->update([
        'order_status' => 'pending'
      ]);
    } else if ($request['order_status'] == 'accepted') {
      $booking->update([
        'order_status' => 'accepted'
      ]);

      $appointmentProcess = new ServicePaymentController();
      // generate an invoice in pdf format
      $invoice = $appointmentProcess->generateInvoice($booking);

      // then, update the invoice field info in database
      $booking->update(['invoice' => $invoice]);

      // send a mail to the customer with the invoice for booking accepted
      $appointmentProcess->prepareMail($booking);
    } else if ($request['order_status'] == 'rejected') {
      //after reject
      $misc = new MiscellaneousController();
      $language = $misc->getLanguage();

      $serviceInfo = null;
      if (($booking->item_type ?? 'service') !== 'package') {
        $serviceInfo = ServiceContent::query()
          ->where('service_id', $booking->service_id)
          ->where('language_id', $language->id)
          ->select('name', 'slug')
          ->firstOrFail();
      }

      $booking->update([
        'order_status' => 'rejected',
        'refund' => 'pending',
      ]);
      if ($booking->vendor_id != 0) {
        $lessAppointmentNum = intval($vendor->total_appointment) + 1;
        //update less appoitnment number
        $vendor->update([
          'total_appointment' => $lessAppointmentNum,
        ]);
      }

      // get the mail template info from db
      $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'service_booking_rejected')->first();
      $mailData['subject'] = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;

      // get the website title info from db
      $info = Basic::select('website_title')->first();

      $service = ($booking->item_type ?? 'service') === 'package'
        ? ($booking->package_name ?? 'Service Package')
        : $serviceInfo->name;
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
    }
    return redirect()->back()->with('success', 'Appointment status update successful!');
  }

  // Reschedule an appointment: change its date and/or time slot, then notify the customer.
  public function reschedule(Request $request, $id)
  {
    $request->validate([
      'booking_date' => ['required', 'date'],
      'start_time' => ['required', 'date_format:H:i'],
      'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
    ]);

    $booking = ServiceBooking::findOrFail($id);

    if ($booking->order_status !== 'pending') {
      return redirect()->back()->with('warning', 'Only pending appointments can be rescheduled.');
    }

    $oldDate = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : '-';
    $oldTime = trim(($booking->start_date ?? '') . ' - ' . ($booking->end_date ?? ''), ' -');

    $booking->update([
      'booking_date' => \Carbon\Carbon::parse($request->booking_date)->toDateString(),
      'start_date' => $request->start_time,
      'end_date' => $request->end_time,
    ]);

    $newDate = \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y');
    $newTime = \Carbon\Carbon::parse($booking->start_date)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($booking->end_date)->format('g:i A');

    $this->sendRescheduleMail($booking, $oldDate, $oldTime, $newDate, $newTime);

    return redirect()->back()->with('success', 'Appointment rescheduled successfully!');
  }

  // Let the customer know their appointment date/time was changed.
  private function sendRescheduleMail($booking, $oldDate, $oldTime, $newDate, $newTime)
  {
    $language = Language::where('is_default', 1)->first();

    $serviceName = optional(
      ServiceContent::where('service_id', $booking->service_id)
        ->where('language_id', $language->id)
        ->first()
    )->name ?? ('Service #' . $booking->service_id);

    $websiteTitle = optional(Basic::select('website_title')->first())->website_title;

    $oldTime = $oldTime !== '' ? $oldTime : '-';
    $body = '<p>Dear ' . e($booking->customer_name) . ',</p>'
      . '<p>The schedule for your appointment <strong>#' . e($booking->order_number) . '</strong> '
      . '(' . e($serviceName) . ') has been updated.</p>'
      . '<p><strong>Previous:</strong> ' . e($oldDate) . ' (' . e($oldTime) . ')<br>'
      . '<strong>New:</strong> ' . e($newDate) . ' (' . e($newTime) . ')</p>'
      . '<p>If this change does not work for you, please contact us.</p>'
      . '<p>Regards,<br>' . e($websiteTitle) . '</p>';

    BasicMailer::sendMail([
      'subject' => 'Your appointment has been rescheduled',
      'body' => $body,
      'recipient' => $booking->customer_email,
    ]);
  }

  // Approve a customer's date-change request: apply the requested slot to the booking.
  public function approveRescheduleRequest($id)
  {
    $booking = ServiceBooking::findOrFail($id);

    if ($booking->reschedule_status !== 'requested') {
      return redirect()->back()->with('warning', 'There is no pending date change request for this appointment.');
    }

    $oldDate = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : '-';
    $oldTime = trim(($booking->start_date ?? '') . ' - ' . ($booking->end_date ?? ''), ' -');

    $booking->update([
      'booking_date' => \Carbon\Carbon::parse($booking->requested_booking_date)->toDateString(),
      'start_date' => $booking->requested_start_time,
      'end_date' => $booking->requested_end_time,
      'reschedule_status' => 'approved',
      'requested_booking_date' => null,
      'requested_start_time' => null,
      'requested_end_time' => null,
    ]);

    $newDate = \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y');
    $newTime = \Carbon\Carbon::parse($booking->start_date)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($booking->end_date)->format('g:i A');

    $this->sendRescheduleMail($booking, $oldDate, $oldTime, $newDate, $newTime);

    return redirect()->back()->with('success', 'Date change request approved.');
  }

  // Reject a customer's date-change request: keep the original schedule, notify the customer.
  public function rejectRescheduleRequest($id)
  {
    $booking = ServiceBooking::findOrFail($id);

    if ($booking->reschedule_status !== 'requested') {
      return redirect()->back()->with('warning', 'There is no pending date change request for this appointment.');
    }

    $requestedDate = \Carbon\Carbon::parse($booking->requested_booking_date)->format('M d, Y');

    $booking->update([
      'reschedule_status' => 'rejected',
      'requested_booking_date' => null,
      'requested_start_time' => null,
      'requested_end_time' => null,
    ]);

    $websiteTitle = optional(Basic::select('website_title')->first())->website_title;
    $body = '<p>Dear ' . e($booking->customer_name) . ',</p>'
      . '<p>Your request to change the date of appointment <strong>#' . e($booking->order_number) . '</strong> '
      . 'to ' . e($requestedDate) . ' could not be approved. Your appointment remains on its original schedule.</p>'
      . '<p>Please contact us if you need to discuss the timing.</p>'
      . '<p>Regards,<br>' . e($websiteTitle) . '</p>';

    BasicMailer::sendMail([
      'subject' => 'Your date change request was declined',
      'body' => $body,
      'recipient' => $booking->customer_email,
    ]);

    return redirect()->back()->with('success', 'Date change request rejected.');
  }

  //refund status change
  public function updateRefundStatus(Request $request, $id)
  {
    $language = Language::where('is_default', 1)->first();
    $language_id = $language->id;
    $appointment = ServiceBooking::with(['serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->findOrFail($id);

    if ($request['refund'] == 'pending') {
      $appointment->update([
        'refund' => 'pending'
      ]);
      //send mail
      $type = 'service_payment_pending';
      payemntStatusMail($type, $id);

      return redirect()->back()->with('success', 'Refund status update successful!');
    } else {
      $appointment->update([
        'refund' => 'refunded'
      ]);

      $arrData = array(
        'type' => 'refund',
        'customer_paid' => $appointment->customer_paid,
        'paymentMethod' => $appointment->payment_method,
        'currencySymbol' => $appointment->currency_symbol,
        'currencySymbolPosition' => $appointment->currency_symbol_position,
        'paymentStatus' => $appointment->payment_status,
        'vendor_id' => $appointment->vendor_id,
        'refund_amount' => $appointment->customer_paid,
      );

      //create transaction for this payment
      $transaction = new TransactionController();
      $transaction->storeTransaction($arrData);

      return redirect()->back()->with('success', 'Refund status update successful!');
    }
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

  public function vendorAssign(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'appointment_id' => 'required|integer|exists:service_bookings,id',
      'vendor_id' => 'required|integer|min:0',
    ], [
      'vendor_id.required' => 'Please select a vendor.',
    ]);

    $validator->after(function ($validator) use ($request) {
      if ((int) $request->vendor_id > 0 && !Vendor::where('id', $request->vendor_id)->where('status', 1)->exists()) {
        $validator->errors()->add('vendor_id', 'Please select an active vendor.');
      }
    });

    if ($validator->fails()) {
      return Response::json(['errors' => $validator->getMessageBag()->toArray()], 400);
    }

    $appointment = ServiceBooking::with('serviceContent')->findOrFail($request->appointment_id);
    $oldVendorId = (int) $appointment->vendor_id;
    $newVendorId = (int) $request->vendor_id;
    $shouldAdjustAppointmentCount = $appointment->order_status !== 'rejected';

    // Warn (but allow override) when the chosen vendor is unavailable for this slot.
    $force = filter_var($request->input('force', false), FILTER_VALIDATE_BOOLEAN);
    if ($newVendorId > 0 && $newVendorId !== $oldVendorId && !$force) {
      $reasons = VendorAvailability::reasons(
        $newVendorId,
        $appointment->booking_date,
        $appointment->start_date,
        $appointment->end_date,
        $appointment->id
      );

      if (!empty($reasons)) {
        $vendor = Vendor::with('vendor_infos')->find($newVendorId);
        $vendorName = $vendor ? (optional($vendor->vendor_infos->first())->name ?? $vendor->username) : 'This vendor';

        return Response::json([
          'status' => 'conflict',
          'message' => $vendorName . ' is unavailable for this appointment: ' . implode('; ', $reasons) . '.',
          'reasons' => $reasons,
        ], 409);
      }
    }

    DB::transaction(function () use ($appointment, $oldVendorId, $newVendorId, $shouldAdjustAppointmentCount) {
      if ($oldVendorId !== $newVendorId && $shouldAdjustAppointmentCount) {
        $this->adjustVendorAppointmentCounts($oldVendorId, $newVendorId);
      }

      $updateData = [
        'vendor_id' => $newVendorId,
      ];

      if ($oldVendorId !== $newVendorId) {
        $updateData['staff_id'] = null;
      }

      $appointment->update($updateData);
    });

    if ($newVendorId !== 0 && $oldVendorId !== $newVendorId) {
      $appointment->refresh();
      $this->sendVendorAssignmentMail($appointment, Vendor::find($newVendorId));
    }

    $request->session()->flash('success', 'Vendor assigned successfully!');
    return Response::json(['status' => 'success'], 200);
  }

  private function getActiveVendors($languageId)
  {
    $vendors = Vendor::with(['vendor_infos' => function ($q) use ($languageId) {
      $q->where('language_id', $languageId);
    }])
      ->where('status', 1)
      ->orderBy('username')
      ->get();

    return $vendors;
  }

  private function adjustVendorAppointmentCounts($oldVendorId, $newVendorId)
  {
    if ($oldVendorId > 0) {
      $oldVendor = Vendor::find($oldVendorId);
      if ($oldVendor) {
        $oldVendor->update([
          'total_appointment' => intval($oldVendor->total_appointment) + 1,
        ]);
      }
    }

    if ($newVendorId > 0) {
      $newVendor = Vendor::find($newVendorId);
      if ($newVendor) {
        $newVendor->update([
          'total_appointment' => max(0, intval($newVendor->total_appointment) - 1),
        ]);
      }
    }
  }

  private function sendVendorAssignmentMail($appointment, $vendor)
  {
    if (!$vendor || empty($vendor->email)) {
      return;
    }

    $language = Language::where('is_default', 1)->first();
    $serviceInfo = ServiceContent::query()
      ->where('service_id', $appointment->service_id)
      ->when($language, function ($query) use ($language) {
        return $query->where('language_id', $language->id);
      })
      ->select('name', 'slug')
      ->first();

    $serviceName = ($appointment->item_type ?? 'service') === 'package'
      ? ($appointment->package_name ?? 'Service Package')
      : ($serviceInfo ? $serviceInfo->name : 'Service #' . $appointment->service_id);
    $serviceLink = $serviceInfo
      ? route('frontend.service.details', ['slug' => $serviceInfo->slug, 'id' => $appointment->service_id])
      : '#';
    $appointmentLink = route('vendor.appointment.details', ['id' => $appointment->id]);
    $vendorName = optional($vendor->vendor_infos->first())->name ?? $vendor->username;
    $websiteTitle = optional(Basic::select('website_title')->first())->website_title;
    $appointmentTime = trim($appointment->start_date . ' to ' . $appointment->end_date);

    $mailData['subject'] = 'New service appointment assigned';
    $mailData['body'] = view('emails.vendor-appointment-assigned', [
      'appointment' => $appointment,
      'appointmentLink' => $appointmentLink,
      'appointmentTime' => $appointmentTime,
      'serviceLink' => $serviceLink,
      'serviceName' => $serviceName,
      'vendorName' => $vendorName,
      'websiteTitle' => $websiteTitle,
    ])->render();
    $mailData['recipient'] = $vendor->email;

    BasicMailer::sendMail($mailData);
  }

  private function deleteAppointment(ServiceBooking $appointment): void
  {
    $serviceId = $appointment->service_id ? (int) $appointment->service_id : null;
    $vendorId = $appointment->vendor_id ? (int) $appointment->vendor_id : null;

    $this->deleteFileIfExists(public_path('assets/file/attachments/service/' . $appointment->attachment));
    $this->deleteFileIfExists(public_path('assets/file/invoices/service/' . $appointment->invoice));

    $appointment->answers()->delete();
    $this->deleteLinkedTransactions($appointment);

    foreach ($appointment->completionImages()->get() as $image) {
      $this->deleteFileIfExists(public_path('assets/img/appointment-completions/' . $image->image));
      $image->delete();
    }

    foreach ($appointment->vendorInvoice()->get() as $invoice) {
      $invoice->delete();
    }

    ServiceReview::where('booking_id', $appointment->id)->delete();
    ServiceReview::refreshRatings($serviceId, $vendorId);

    $appointment->delete();
  }

  private function deleteLinkedTransactions(ServiceBooking $appointment): void
  {
    if (!Schema::hasTable('transactions')) {
      return;
    }

    $query = DB::table('transactions');
    $hasLinkedColumn = false;

    if (Schema::hasColumn('transactions', 'booking_id')) {
      $query->orWhere('booking_id', $appointment->id);
      $hasLinkedColumn = true;
    }

    if (Schema::hasColumn('transactions', 'service_booking_id')) {
      $query->orWhere('service_booking_id', $appointment->id);
      $hasLinkedColumn = true;
    }

    if ($appointment->order_number && Schema::hasColumn('transactions', 'order_number')) {
      $query->orWhere('order_number', $appointment->order_number);
      $hasLinkedColumn = true;
    }

    if ($hasLinkedColumn) {
      $query->delete();
    }
  }

  private function deleteFileIfExists(?string $path): void
  {
    if ($path && is_file($path)) {
      @unlink($path);
    }
  }

  public function destroy($id)
  {
    $appointment = ServiceBooking::find($id);

    if (!$appointment) {
      return redirect()->back()->with('warning', 'Appointment not found.');
    }

    DB::transaction(function () use ($appointment) {
      $this->deleteAppointment($appointment);
    });

    return redirect()->back()->with('success', 'Appointment delete successfully!');
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    DB::transaction(function () use ($ids) {
      foreach ((array) $ids as $id) {
        $appointment = ServiceBooking::find($id);

        if ($appointment) {
          $this->deleteAppointment($appointment);
        }
      }
    });

    $request->session()->flash('success', 'Appointments delete successfully!');

    return Response::json(['status' => 'success'], 200);
  }
}
