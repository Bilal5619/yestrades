<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceContent;
use App\Models\Vendor;
use Auth;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
  public function appointment(Request $request)
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();
    $language_id = $language->id;

    $information['bgImg'] = $misc->getBreadcrumb();
    $information['pageHeading'] = $misc->getPageHeading($language);


    $information['appointments'] = ServiceBooking::join('service_contents', 'service_bookings.service_id', '=', 'service_contents.service_id')
    ->where('service_contents.language_id', $language_id)
    ->when($request->search_appointment, function ($query) use ($request) {
      $query->where(function ($subQuery) use ($request) {
        $subQuery->where('service_bookings.order_number', 'like', '%' . $request->search_appointment . '%')
          ->orWhere('service_contents.name', 'like', '%' . $request->search_appointment . '%');
      });
    })
    ->where('service_bookings.user_id', Auth::guard('web')->user()->id)
    ->orderBy('service_bookings.id', 'desc')
    ->select(
      'service_contents.name',
      'service_contents.slug',
      'service_bookings.id',
      'service_bookings.start_date',
      'service_bookings.end_date',
      'service_bookings.booking_date',
      'service_bookings.vendor_id',
      'service_bookings.order_status',
      'service_bookings.service_id',
    )
    ->paginate(10);


    return view('frontend.user.appointment.index', $information);
  }

  public function details($id)
  {
    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();
    $language_id = $language->id;


    $queryResult['bgImg'] = $misc->getBreadcrumb();
    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $appointment = ServiceBooking::with(['completionImages', 'serviceContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->findOrFail($id);

    if ($appointment) {
      if ($appointment->user_id != Auth::guard('web')->user()->id) {
        return redirect()->route('user.dashboard');
      }
      $queryResult['appointment'] = $appointment;

      if ($appointment->vendor_id != 0) {
        $queryResult['vendor'] = Vendor::join('vendor_infos', 'vendor_infos.vendor_id', 'vendors.id')
          ->where('vendors.id', $appointment->vendor_id)
          ->select('vendors.show_email_addresss', 'vendors.show_phone_number', 'vendors.email', 'vendors.phone', 'vendors.username', 'vendor_infos.name', 'vendor_infos.country', 'vendor_infos.city', 'vendor_infos.address', 'vendor_infos.details')
          ->firstOrFail();
      } else {
        $queryResult['vendor'] = Admin::whereNull('role_id')->firstOrFail();
      }

      return view('frontend.user.appointment.details', $queryResult);
    } else {
      return view('errors.404');
    }
  }

  // Customer submits a date/time change REQUEST. The live appointment is not changed
  // until an admin approves it.
  public function reschedule(Request $request, $id)
  {
    $request->validate([
      'booking_date' => ['required', 'date'],
      'start_time' => ['required', 'date_format:H:i'],
      'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
    ]);

    $booking = ServiceBooking::findOrFail($id);

    if ($booking->user_id != Auth::guard('web')->user()->id) {
      return redirect()->route('user.dashboard');
    }

    if ($booking->order_status !== 'pending') {
      return redirect()->back()->with('warning', 'Only pending appointments can be rescheduled.');
    }

    $booking->update([
      'requested_booking_date' => \Carbon\Carbon::parse($request->booking_date)->toDateString(),
      'requested_start_time' => $request->start_time,
      'requested_end_time' => $request->end_time,
      'reschedule_status' => 'requested',
    ]);

    $this->notifyAdminOfRescheduleRequest($booking);

    return redirect()->back()->with('success', 'Your date change request has been submitted for approval.');
  }

  // Customer withdraws a pending date-change request.
  public function cancelRescheduleRequest($id)
  {
    $booking = ServiceBooking::findOrFail($id);

    if ($booking->user_id != Auth::guard('web')->user()->id) {
      return redirect()->route('user.dashboard');
    }

    if ($booking->reschedule_status !== 'requested') {
      return redirect()->back()->with('warning', 'There is no pending request to cancel.');
    }

    $booking->update([
      'requested_booking_date' => null,
      'requested_start_time' => null,
      'requested_end_time' => null,
      'reschedule_status' => null,
    ]);

    return redirect()->back()->with('success', 'Your date change request has been cancelled.');
  }

  // Notify the admin that a customer is requesting a new date/time.
  private function notifyAdminOfRescheduleRequest($booking)
  {
    $recipient = optional(Admin::whereNull('role_id')->first())->email;

    if (empty($recipient)) {
      return;
    }

    $misc = new MiscellaneousController();
    $language = $misc->getLanguage();

    $serviceName = optional(
      ServiceContent::where('service_id', $booking->service_id)
        ->where('language_id', $language->id)
        ->first()
    )->name ?? ('Service #' . $booking->service_id);

    $websiteTitle = optional(Basic::select('website_title')->first())->website_title;

    $currentDate = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : '-';
    $currentTime = trim(($booking->start_date ?? '') . ' - ' . ($booking->end_date ?? ''), ' -') ?: '-';
    $requestedDate = \Carbon\Carbon::parse($booking->requested_booking_date)->format('M d, Y');
    $requestedTime = \Carbon\Carbon::parse($booking->requested_start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($booking->requested_end_time)->format('g:i A');

    $body = '<p>Hello,</p>'
      . '<p>The customer <strong>' . e($booking->customer_name) . '</strong> has requested a date/time change for appointment '
      . '<strong>#' . e($booking->order_number) . '</strong> (' . e($serviceName) . ').</p>'
      . '<p><strong>Current:</strong> ' . e($currentDate) . ' (' . e($currentTime) . ')<br>'
      . '<strong>Requested:</strong> ' . e($requestedDate) . ' (' . e($requestedTime) . ')</p>'
      . '<p>Please review and approve or reject this request from the admin panel.</p>'
      . '<p>Regards,<br>' . e($websiteTitle) . '</p>';

    BasicMailer::sendMail([
      'subject' => 'Date change request from a customer',
      'body' => $body,
      'recipient' => $recipient,
    ]);
  }
}
