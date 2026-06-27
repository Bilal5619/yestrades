<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Services\ServiceBooking;
use App\Models\VendorAvailabilityException;
use App\Models\VendorWeeklyHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Response;

class AvailabilityController extends Controller
{
  // Days of the week using Carbon's dayOfWeek index (0 = Sunday).
  private $days = [
    0 => 'Sunday',
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
  ];

  public function index()
  {
    $vendorId = Auth::guard('vendor')->user()->id;

    $information['langs'] = Language::all();
    $information['days'] = $this->days;

    $existing = VendorWeeklyHour::where('vendor_id', $vendorId)->get()->keyBy('day_of_week');
    $weekly = [];
    foreach ($this->days as $index => $name) {
      $row = $existing->get($index);
      $weekly[$index] = [
        'name' => $name,
        'is_open' => $row ? (bool) $row->is_open : ($index >= 1 && $index <= 5),
        'start_time' => $row && $row->start_time ? Carbon::parse($row->start_time)->format('H:i') : '09:00',
        'end_time' => $row && $row->end_time ? Carbon::parse($row->end_time)->format('H:i') : '17:00',
      ];
    }
    $information['weekly'] = $weekly;

    $information['exceptions'] = VendorAvailabilityException::where('vendor_id', $vendorId)
      ->orderBy('date')
      ->get();

    return view('vendors.availability.index', $information);
  }

  // JSON feed: own appointments + blocked exception dates for the calendar.
  public function events(Request $request)
  {
    $vendorId = Auth::guard('vendor')->user()->id;
    $language = Language::where('is_default', 1)->first();

    $bookings = ServiceBooking::with(['serviceContent' => function ($q) use ($language) {
      $q->where('language_id', $language->id);
    }])
      ->where('vendor_id', $vendorId)
      ->where('order_status', '!=', 'rejected')
      ->when($request->filled('start') && $request->filled('end'), function ($query) use ($request) {
        return $query->whereBetween('booking_date', [
          Carbon::parse($request->start)->toDateString(),
          Carbon::parse($request->end)->toDateString(),
        ]);
      })
      ->get();

    $events = $bookings->map(function (ServiceBooking $booking) {
      $serviceName = optional($booking->serviceContent->first())->name ?? ('Service #' . $booking->service_id);
      $date = $booking->booking_date ? Carbon::parse($booking->booking_date)->toDateString() : null;
      $allDay = !($booking->start_date && $booking->end_date);

      return [
        'id' => 'booking_' . $booking->id,
        'title' => $serviceName . ' (#' . $booking->order_number . ')',
        'start' => $allDay ? $date : $date . 'T' . Carbon::parse($booking->start_date)->format('H:i:s'),
        'end' => $allDay ? null : $date . 'T' . Carbon::parse($booking->end_date)->format('H:i:s'),
        'allDay' => $allDay,
        'color' => '#4e73df',
        'url' => route('vendor.appointment.details', ['id' => $booking->id]),
      ];
    });

    $exceptions = VendorAvailabilityException::where('vendor_id', $vendorId)->get()->map(function ($exception) {
      $date = Carbon::parse($exception->date)->toDateString();
      $fullDay = $exception->is_full_day || !$exception->start_time || !$exception->end_time;

      return [
        'id' => 'exception_' . $exception->id,
        'title' => '🚫 ' . ($exception->reason ?: 'Unavailable'),
        'start' => $fullDay ? $date : $date . 'T' . Carbon::parse($exception->start_time)->format('H:i:s'),
        'end' => $fullDay ? null : $date . 'T' . Carbon::parse($exception->end_time)->format('H:i:s'),
        'allDay' => $fullDay,
        'color' => '#e74a3b',
        'display' => 'background',
      ];
    });

    return Response::json($events->concat($exceptions)->values());
  }

  public function saveWeekly(Request $request)
  {
    $vendorId = Auth::guard('vendor')->user()->id;

    foreach ($this->days as $index => $name) {
      $isOpen = $request->has('is_open_' . $index);
      $start = $request->input('start_time_' . $index);
      $end = $request->input('end_time_' . $index);

      VendorWeeklyHour::updateOrCreate(
        ['vendor_id' => $vendorId, 'day_of_week' => $index],
        [
          'is_open' => $isOpen,
          'start_time' => $isOpen ? $start : null,
          'end_time' => $isOpen ? $end : null,
        ]
      );
    }

    return redirect()->back()->with('success', 'Weekly availability updated successfully!');
  }

  public function storeException(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'date' => 'required|date',
      'is_full_day' => 'nullable',
      'start_time' => 'nullable|date_format:H:i',
      'end_time' => 'nullable|date_format:H:i|after:start_time',
      'reason' => 'nullable|string|max:191',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $vendorId = Auth::guard('vendor')->user()->id;
    $fullDay = $request->has('is_full_day') || !$request->filled('start_time') || !$request->filled('end_time');

    VendorAvailabilityException::create([
      'vendor_id' => $vendorId,
      'date' => $request->date,
      'is_full_day' => $fullDay,
      'start_time' => $fullDay ? null : $request->start_time,
      'end_time' => $fullDay ? null : $request->end_time,
      'reason' => $request->reason,
    ]);

    return redirect()->back()->with('success', 'Unavailable date added successfully!');
  }

  public function destroyException($id)
  {
    $vendorId = Auth::guard('vendor')->user()->id;

    VendorAvailabilityException::where('id', $id)
      ->where('vendor_id', $vendorId)
      ->delete();

    return redirect()->back()->with('success', 'Unavailable date removed successfully!');
  }
}
