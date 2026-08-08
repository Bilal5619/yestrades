<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company\Announcement;
use App\Models\Company\AnnouncementRead;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeAttendance;
use App\Models\Company\EmployeeHoliday;
use App\Models\Company\EmployeeJob;
use App\Models\Company\EmployeePayslip;
use App\Models\Company\EmployeeShift;
use App\Models\Company\EmployeeTrainingRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeMobileController extends Controller
{
  public function profile(Request $request): JsonResponse
  {
    /** @var Employee $employee */
    $employee = $request->user()->load('manager');

    return response()->json([
      'success' => true,
      'data' => ['profile' => $this->profilePayload($employee)],
    ]);
  }

  public function updateProfile(Request $request): JsonResponse
  {
    /** @var Employee $employee */
    $employee = $request->user();

    $data = $request->validate([
      'personal_email' => ['nullable', 'email', 'max:255'],
      'phone' => ['nullable', 'string', 'max:50'],
      'address_line_1' => ['nullable', 'string', 'max:255'],
      'address_line_2' => ['nullable', 'string', 'max:255'],
      'city' => ['nullable', 'string', 'max:255'],
      'county' => ['nullable', 'string', 'max:255'],
      'postcode' => ['nullable', 'string', 'max:20'],
      'country' => ['nullable', 'string', 'max:120'],
      'emergency_contact_name' => ['nullable', 'string', 'max:255'],
      'emergency_contact_relationship' => ['nullable', 'string', 'max:255'],
      'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
      'emergency_contact_alternative_phone' => ['nullable', 'string', 'max:50'],
      'emergency_contact_address' => ['nullable', 'string', 'max:2000'],
    ]);

    $employee->fill($data)->save();

    return response()->json([
      'success' => true,
      'message' => 'Profile updated.',
      'data' => ['profile' => $this->profilePayload($employee->fresh('manager'))],
    ]);
  }

  public function holidays(Request $request): JsonResponse
  {
    /** @var Employee $employee */
    $employee = $request->user();
    $year = (int) $request->query('year', now()->year);
    $allowance = $employee->holidayAllowances()->where('holiday_year', $year)->first();

    $requests = $employee->holidays()
      ->whereYear('start_date', $year)
      ->orderByDesc('start_date')
      ->get()
      ->map(fn (EmployeeHoliday $holiday) => $this->holidayPayload($holiday));

    $pendingDays = $employee->holidays()
      ->where('status', 'pending')
      ->whereYear('start_date', $year)
      ->sum('total_days');

    $approvedUpcomingDays = $employee->holidays()
      ->where('status', 'approved')
      ->whereDate('start_date', '>=', today())
      ->whereYear('start_date', $year)
      ->sum('total_days');

    return response()->json([
      'success' => true,
      'data' => [
        'summary' => [
          'year' => $year,
          'accrued' => $allowance ? round((float) $allowance->total_allowance, 2) : 0,
          'taken' => $allowance ? round((float) $allowance->approved_days_used, 2) : 0,
          'approved_upcoming' => round((float) $approvedUpcomingDays, 2),
          'pending' => round((float) $pendingDays, 2),
          'carried_over' => $allowance ? round((float) $allowance->carried_over, 2) : 0,
          'adjustment' => $allowance ? round((float) $allowance->adjustment, 2) : 0,
          'remaining' => $allowance ? round((float) $allowance->remaining_allowance, 2) : 0,
        ],
        'requests' => $requests,
      ],
    ]);
  }

  public function requestHoliday(Request $request): JsonResponse
  {
    /** @var Employee $employee */
    $employee = $request->user();

    $data = $request->validate([
      'holiday_type' => ['nullable', Rule::in(['annual_leave', 'sick_leave', 'unpaid_leave', 'maternity_leave', 'paternity_leave', 'compassionate_leave', 'other'])],
      'start_date' => ['required', 'date', 'after_or_equal:today'],
      'end_date' => ['required', 'date', 'after_or_equal:start_date'],
      'start_period' => ['nullable', Rule::in(['full_day', 'morning', 'afternoon'])],
      'end_period' => ['nullable', Rule::in(['full_day', 'morning', 'afternoon'])],
      'reason' => ['nullable', 'string', 'max:2000'],
    ]);

    $start = \Carbon\Carbon::parse($data['start_date']);
    $end = \Carbon\Carbon::parse($data['end_date']);
    $days = $start->diffInDays($end) + 1;

    if (($data['start_period'] ?? 'full_day') !== 'full_day') {
      $days -= 0.5;
    }

    if (!$start->isSameDay($end) && ($data['end_period'] ?? 'full_day') !== 'full_day') {
      $days -= 0.5;
    }

    $holiday = $employee->holidays()->create([
      'holiday_type' => $data['holiday_type'] ?? 'annual_leave',
      'start_date' => $start->toDateString(),
      'end_date' => $end->toDateString(),
      'start_period' => $data['start_period'] ?? 'full_day',
      'end_period' => $data['end_period'] ?? 'full_day',
      'total_days' => max(0.5, $days),
      'reason' => $data['reason'] ?? null,
      'status' => 'pending',
      'requested_at' => now(),
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Holiday request submitted.',
      'data' => ['request' => $this->holidayPayload($holiday)],
    ], 201);
  }

  public function cancelHoliday(Request $request, EmployeeHoliday $holiday): JsonResponse
  {
    /** @var Employee $employee */
    $employee = $request->user();
    abort_unless($holiday->employee_id === $employee->id, 404);

    if ($holiday->status !== 'pending') {
      return response()->json([
        'success' => false,
        'message' => 'Only pending holiday requests can be cancelled in the staff app.',
      ], 422);
    }

    $holiday->update([
      'status' => 'cancelled',
      'cancelled_at' => now(),
      'cancellation_reason' => $request->input('reason'),
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Holiday request cancelled.',
      'data' => ['request' => $this->holidayPayload($holiday->fresh())],
    ]);
  }

  public function training(Request $request): JsonResponse
  {
    $records = $request->user()->trainingRecords()
      ->with('trainingCourse')
      ->orderByRaw("FIELD(status, 'expired', 'scheduled', 'in_progress', 'not_started', 'completed', 'cancelled')")
      ->orderBy('expiry_date')
      ->get()
      ->map(fn (EmployeeTrainingRecord $record) => [
        'id' => $record->id,
        'course_name' => optional($record->trainingCourse)->name ?? 'Training course',
        'description' => optional($record->trainingCourse)->description,
        'provider_name' => $record->provider_name,
        'scheduled_date' => optional($record->scheduled_date)->toDateString(),
        'started_at' => optional($record->started_at)->toIso8601String(),
        'completed_at' => optional($record->completed_at)->toIso8601String(),
        'expiry_date' => optional($record->expiry_date)->toDateString(),
        'status' => $record->status,
        'status_label' => $record->status_name,
        'certificate_number' => $record->certificate_number,
        'has_certificate' => (bool) $record->certificate_path,
        'is_expired' => $record->is_expired,
        'is_expiring_soon' => $record->is_expiring_soon,
      ]);

    return response()->json(['success' => true, 'data' => ['training' => $records]]);
  }

  public function downloadTrainingCertificate(Request $request, EmployeeTrainingRecord $record)
  {
    abort_unless($record->employee_id === $request->user()->id, 404);
    abort_unless($record->certificate_path && Storage::disk('local')->exists($record->certificate_path), 404);

    return Storage::disk('local')->download($record->certificate_path);
  }

  public function contract(Request $request): JsonResponse
  {
    $contracts = $request->user()->contracts()
      ->with('template')
      ->orderByDesc('version')
      ->orderByDesc('generated_at')
      ->get()
      ->map(fn ($contract) => [
        'id' => $contract->id,
        'title' => optional($contract->template)->title ?? optional($contract->template)->name ?? 'Employment Contract',
        'type' => optional($contract->template)->category,
        'effective_date' => optional($contract->generated_at)->toDateString(),
        'status' => $contract->status,
        'version' => $contract->version,
        'generated_at' => optional($contract->generated_at)->toIso8601String(),
        'signed_at' => optional($contract->signed_at)->toIso8601String(),
        'signed_status' => $contract->signed_at ? 'signed' : 'not_signed',
        'has_download' => (bool) ($contract->signed_file_path ?: $contract->generated_file_path),
      ]);

    return response()->json([
      'success' => true,
      'data' => [
        'current_contract' => $contracts->first(),
        'contracts' => $contracts,
      ],
    ]);
  }

  public function downloadContract(Request $request, int $id)
  {
    $contract = $request->user()->contracts()->findOrFail($id);
    $path = $contract->signed_file_path ?: $contract->generated_file_path;
    $filename = $contract->signed_filename ?: $contract->generated_filename ?: 'employment-contract.pdf';

    abort_unless($path && Storage::disk('local')->exists($path), 404);

    return Storage::disk('local')->download($path, $filename, ['Content-Type' => 'application/pdf']);
  }

  public function payslips(Request $request): JsonResponse
  {
    $query = $request->user()->payslips();

    if ($request->filled('year')) {
      $query->whereYear('payment_date', (int) $request->query('year'));
    }

    if ($request->filled('search')) {
      $search = $request->query('search');
      $query->where(function ($q) use ($search) {
        $q->where('pay_period', 'like', "%{$search}%")
          ->orWhere('original_filename', 'like', "%{$search}%")
          ->orWhereYear('payment_date', $search);
      });
    }

    $payslips = $query->orderByDesc('payment_date')->get()->map(fn (EmployeePayslip $payslip) => $this->payslipPayload($payslip));

    $years = $request->user()->payslips()
      ->selectRaw('YEAR(payment_date) as year')
      ->distinct()
      ->orderByDesc('year')
      ->pluck('year');

    return response()->json(['success' => true, 'data' => ['payslips' => $payslips, 'years' => $years]]);
  }

  public function payslip(Request $request, EmployeePayslip $payslip): JsonResponse
  {
    abort_unless($payslip->employee_id === $request->user()->id, 404);

    return response()->json(['success' => true, 'data' => ['payslip' => $this->payslipPayload($payslip)]]);
  }

  public function downloadPayslip(Request $request, EmployeePayslip $payslip)
  {
    abort_unless($payslip->employee_id === $request->user()->id, 404);
    abort_unless(Storage::disk('local')->exists($payslip->file_path), 404);

    return Storage::disk('local')->download($payslip->file_path, $payslip->original_filename, [
      'Content-Type' => $payslip->mime_type ?: 'application/pdf',
    ]);
  }

  public function announcements(Request $request): JsonResponse
  {
    $employee = $request->user();
    $readIds = $employee->announcementReads()->pluck('announcement_id')->all();

    $announcements = Announcement::query()
      ->whereNotNull('published_at')
      ->where('published_at', '<=', now())
      ->orderByDesc('published_at')
      ->get()
      ->map(fn (Announcement $announcement) => [
        'id' => $announcement->id,
        'title' => $announcement->title,
        'body' => $announcement->body,
        'is_urgent' => $announcement->is_urgent,
        'published_at' => optional($announcement->published_at)->toIso8601String(),
        'is_read' => in_array($announcement->id, $readIds, true),
      ]);

    return response()->json(['success' => true, 'data' => ['announcements' => $announcements]]);
  }

  public function markAnnouncementRead(Request $request, Announcement $announcement): JsonResponse
  {
    abort_unless($announcement->isPublished(), 404);

    AnnouncementRead::firstOrCreate([
      'announcement_id' => $announcement->id,
      'employee_id' => $request->user()->id,
    ], [
      'read_at' => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Announcement marked read.']);
  }

  public function notifications(Request $request): JsonResponse
  {
    $employee = $request->user();
    $notifications = collect();

    Announcement::query()
      ->whereNotNull('published_at')
      ->where('published_at', '<=', now())
      ->latest('published_at')
      ->limit(10)
      ->get()
      ->each(fn ($announcement) => $notifications->push([
        'id' => 'announcement-'.$announcement->id,
        'type' => $announcement->is_urgent ? 'urgent_company_message' : 'announcement',
        'title' => $announcement->title,
        'body' => $announcement->body,
        'target' => ['screen' => 'announcements', 'id' => $announcement->id],
        'created_at' => optional($announcement->published_at)->toIso8601String(),
      ]));

    $employee->jobs()
      ->whereDate('scheduled_date', today()->addDay())
      ->whereNotIn('status', ['completed', 'cancelled'])
      ->get()
      ->each(fn ($job) => $notifications->push([
        'id' => 'job-'.$job->id,
        'type' => 'next_day_job_reminder',
        'title' => 'Job tomorrow',
        'body' => $job->title,
        'target' => ['screen' => 'jobs', 'id' => $job->id],
        'created_at' => now()->toIso8601String(),
      ]));

    $employee->shifts()
      ->whereDate('shift_date', today()->addDay())
      ->where('status', 'scheduled')
      ->get()
      ->each(fn ($shift) => $notifications->push([
        'id' => 'shift-'.$shift->id,
        'type' => 'next_day_shift_reminder',
        'title' => 'Shift tomorrow',
        'body' => $shift->start_time.' - '.$shift->end_time,
        'target' => ['screen' => 'shifts', 'id' => $shift->id],
        'created_at' => now()->toIso8601String(),
      ]));

    return response()->json(['success' => true, 'data' => ['notifications' => $notifications->values()]]);
  }

  public function jobs(Request $request): JsonResponse
  {
    $start = \Carbon\Carbon::parse($request->query('start', today()->startOfWeek()->toDateString()))->startOfDay();
    $end = (clone $start)->addDays(6)->endOfDay();

    $jobs = $request->user()->jobs()
      ->whereBetween('scheduled_date', [$start->toDateString(), $end->toDateString()])
      ->orderBy('scheduled_date')
      ->orderBy('scheduled_start_time')
      ->get()
      ->map(fn (EmployeeJob $job) => $this->jobPayload($job));

    return response()->json([
      'success' => true,
      'data' => [
        'week' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        'jobs' => $jobs,
      ],
    ]);
  }

  public function job(Request $request, EmployeeJob $job): JsonResponse
  {
    abort_unless($job->employee_id === $request->user()->id, 404);

    return response()->json(['success' => true, 'data' => ['job' => $this->jobPayload($job)]]);
  }

  public function updateJobStatus(Request $request, EmployeeJob $job): JsonResponse
  {
    abort_unless($job->employee_id === $request->user()->id, 404);

    $data = $request->validate([
      'status' => ['required', Rule::in(['accepted', 'en_route', 'on_site', 'completed'])],
    ]);

    if ($job->status === 'cancelled') {
      return response()->json(['success' => false, 'message' => 'Cancelled jobs cannot be updated.'], 422);
    }

    $job->update(['status' => $data['status']]);

    return response()->json(['success' => true, 'message' => 'Job status updated.', 'data' => ['job' => $this->jobPayload($job->fresh())]]);
  }

  public function startJob(Request $request, EmployeeJob $job): JsonResponse
  {
    $employee = $request->user();
    abort_unless($job->employee_id === $employee->id, 404);

    if (in_array($job->status, ['cancelled', 'completed', 'awaiting_review'], true)) {
      return response()->json(['success' => false, 'message' => 'This job cannot be started.'], 422);
    }

    if ($job->started_at !== null) {
      return response()->json(['success' => false, 'message' => 'This job has already been started.'], 422);
    }

    if ($this->hasActiveJob($employee)) {
      return response()->json(['success' => false, 'message' => 'You already have another job in progress. Finish it before starting a new one.'], 422);
    }

    if ($this->hasActiveShiftClockIn($employee)) {
      return response()->json(['success' => false, 'message' => 'You are already clocked in on a shift. Clock out before starting a job.'], 422);
    }

    $data = $request->validate([
      'device_timestamp' => ['nullable', 'date'],
      'latitude' => ['nullable', 'numeric', 'between:-90,90'],
      'longitude' => ['nullable', 'numeric', 'between:-180,180'],
      'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:10000'],
      'device_platform' => ['nullable', 'string', 'max:80'],
    ]);

    $job->update(['started_at' => now(), 'status' => 'in_progress']);

    $employee->attendances()->create([
      'job_id' => $job->id,
      'clock_type' => 'in',
      'server_timestamp' => now(),
      'device_timestamp' => $data['device_timestamp'] ?? null,
      'latitude' => $data['latitude'] ?? null,
      'longitude' => $data['longitude'] ?? null,
      'accuracy_meters' => $data['accuracy_meters'] ?? null,
      'expected_location' => $job->site_address,
      'device_platform' => $data['device_platform'] ?? null,
    ]);

    return response()->json(['success' => true, 'message' => 'Job started.', 'data' => ['job' => $this->jobPayload($job->fresh())]]);
  }

  public function finishJob(Request $request, EmployeeJob $job): JsonResponse
  {
    $employee = $request->user();
    abort_unless($job->employee_id === $employee->id, 404);

    if ($job->started_at === null) {
      return response()->json(['success' => false, 'message' => 'This job has not been started yet.'], 422);
    }

    if ($job->finished_at !== null) {
      return response()->json(['success' => false, 'message' => 'This job has already been finished.'], 422);
    }

    $data = $request->validate([
      'device_timestamp' => ['nullable', 'date'],
      'latitude' => ['nullable', 'numeric', 'between:-90,90'],
      'longitude' => ['nullable', 'numeric', 'between:-180,180'],
      'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:10000'],
      'device_platform' => ['nullable', 'string', 'max:80'],
    ]);

    $job->update(['finished_at' => now(), 'status' => 'completed']);

    $employee->attendances()->create([
      'job_id' => $job->id,
      'clock_type' => 'out',
      'server_timestamp' => now(),
      'device_timestamp' => $data['device_timestamp'] ?? null,
      'latitude' => $data['latitude'] ?? null,
      'longitude' => $data['longitude'] ?? null,
      'accuracy_meters' => $data['accuracy_meters'] ?? null,
      'expected_location' => $job->site_address,
      'device_platform' => $data['device_platform'] ?? null,
    ]);

    return response()->json(['success' => true, 'message' => 'Job finished.', 'data' => ['job' => $this->jobPayload($job->fresh())]]);
  }

  // "Active" means started but not finished/clocked-out yet — used to block
  // an employee from starting a second job, or starting a job while still
  // clocked in on a shift (and vice versa in clock()).
  private function hasActiveJob(Employee $employee, ?int $excludeJobId = null): bool
  {
    return $employee->jobs()
      ->whereNotNull('started_at')
      ->whereNull('finished_at')
      ->when($excludeJobId, fn ($query) => $query->where('id', '!=', $excludeJobId))
      ->exists();
  }

  private function hasActiveShiftClockIn(Employee $employee): bool
  {
    foreach ($employee->shifts()->where('status', 'scheduled')->with('attendances')->get() as $shift) {
      $last = $shift->attendances->sortByDesc('server_timestamp')->first();
      if ($last && $last->clock_type === 'in') {
        return true;
      }
    }

    return false;
  }

  public function shifts(Request $request): JsonResponse
  {
    $start = \Carbon\Carbon::parse($request->query('start', today()->startOfWeek()->toDateString()))->startOfDay();
    $end = (clone $start)->addDays(6)->endOfDay();

    $shifts = $request->user()->shifts()
      ->with('attendances')
      ->whereBetween('shift_date', [$start->toDateString(), $end->toDateString()])
      ->orderBy('shift_date')
      ->orderBy('start_time')
      ->get()
      ->map(fn (EmployeeShift $shift) => $this->shiftPayload($shift));

    $history = $request->user()->attendances()
      ->with('shift')
      ->latest('server_timestamp')
      ->limit(25)
      ->get()
      ->map(fn (EmployeeAttendance $attendance) => $this->attendancePayload($attendance));

    return response()->json([
      'success' => true,
      'data' => [
        'week' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        'shifts' => $shifts,
        'attendance_history' => $history,
      ],
    ]);
  }

  public function clock(Request $request): JsonResponse
  {
    $employee = $request->user();

    if (!$employee->canUseManualAttendance()) {
      return response()->json([
        'success' => false,
        'message' => 'Manual clock in/out is not enabled for your account. Please use your assigned jobs to clock in.',
      ], 403);
    }

    $data = $request->validate([
      'shift_id' => ['required', 'integer'],
      'clock_type' => ['required', Rule::in(['in', 'out'])],
      'device_timestamp' => ['nullable', 'date'],
      'latitude' => ['required', 'numeric', 'between:-90,90'],
      'longitude' => ['required', 'numeric', 'between:-180,180'],
      'accuracy_meters' => ['required', 'numeric', 'min:0', 'max:10000'],
      'device_platform' => ['nullable', 'string', 'max:80'],
    ]);

    $shift = $employee->shifts()->where('status', 'scheduled')->findOrFail($data['shift_id']);

    $lastClock = $shift->attendances()->latest('server_timestamp')->first();

    if ($data['clock_type'] === 'out' && !$lastClock) {
      return response()->json([
        'success' => false,
        'message' => 'You must clock in before you can clock out.',
      ], 422);
    }

    if ($lastClock && $lastClock->clock_type === $data['clock_type']) {
      return response()->json([
        'success' => false,
        'message' => 'This shift already has the latest Clock '.ucfirst($data['clock_type']).' action.',
      ], 422);
    }

    if ($data['clock_type'] === 'in' && $this->hasActiveJob($employee)) {
      return response()->json([
        'success' => false,
        'message' => 'You already have a job in progress. Finish it before clocking in on a shift.',
      ], 422);
    }

    $attendance = $employee->attendances()->create([
      'shift_id' => $shift->id,
      'clock_type' => $data['clock_type'],
      'server_timestamp' => now(),
      'device_timestamp' => $data['device_timestamp'] ?? null,
      'latitude' => $data['latitude'],
      'longitude' => $data['longitude'],
      'accuracy_meters' => $data['accuracy_meters'],
      'expected_location' => $shift->work_location,
      'within_geofence' => null,
      'device_platform' => $data['device_platform'] ?? null,
    ]);

    if ($data['clock_type'] === 'out') {
      $shift->update(['status' => 'completed']);
    }

    return response()->json([
      'success' => true,
      'message' => 'Clock '.ucfirst($data['clock_type']).' recorded.',
      'data' => [
        'attendance' => $this->attendancePayload($attendance->fresh('shift')),
        'shift' => $this->shiftPayload($shift->fresh('attendances')),
      ],
    ], 201);
  }

  private function profilePayload(Employee $employee): array
  {
    return [
      'personal' => [
        'full_name' => $employee->full_name,
        'first_name' => $employee->first_name,
        'last_name' => $employee->last_name,
        'work_email' => $employee->work_email,
        'personal_email' => $employee->personal_email,
        'phone' => $employee->phone,
        'address_line_1' => $employee->address_line_1,
        'address_line_2' => $employee->address_line_2,
        'city' => $employee->city,
        'county' => $employee->county,
        'postcode' => $employee->postcode,
        'country' => $employee->country,
        'emergency_contact_name' => $employee->emergency_contact_name,
        'emergency_contact_relationship' => $employee->emergency_contact_relationship,
        'emergency_contact_phone' => $employee->emergency_contact_phone,
        'emergency_contact_alternative_phone' => $employee->emergency_contact_alternative_phone,
        'emergency_contact_address' => $employee->emergency_contact_address,
        'national_insurance_number' => $employee->national_insurance_number ? '****'.substr($employee->national_insurance_number, -4) : null,
      ],
      'employment' => [
        'employee_number' => $employee->employee_number,
        'job_title' => $employee->job_title,
        'department' => $employee->department,
        'manager' => optional($employee->manager)->full_name,
        'employment_type' => $employee->employment_type,
        'start_date' => optional($employee->start_date)->toDateString(),
        'work_location' => $employee->work_location,
        'contracted_hours' => $employee->normal_weekly_hours,
        'employment_status' => $employee->employment_status,
      ],
    ];
  }

  private function holidayPayload(EmployeeHoliday $holiday): array
  {
    return [
      'id' => $holiday->id,
      'holiday_type' => $holiday->holiday_type,
      'holiday_type_label' => $holiday->holiday_type_name,
      'start_date' => optional($holiday->start_date)->toDateString(),
      'end_date' => optional($holiday->end_date)->toDateString(),
      'start_period' => $holiday->start_period,
      'end_period' => $holiday->end_period,
      'total_days' => round((float) $holiday->total_days, 2),
      'reason' => $holiday->reason,
      'status' => $holiday->status,
      'status_label' => $holiday->status_name,
      'rejection_reason' => $holiday->rejection_reason,
      'can_cancel' => $holiday->status === 'pending',
    ];
  }

  private function payslipPayload(EmployeePayslip $payslip): array
  {
    return [
      'id' => $payslip->id,
      'pay_period' => $payslip->pay_period,
      'payment_date' => optional($payslip->payment_date)->toDateString(),
      'original_filename' => $payslip->original_filename,
      'mime_type' => $payslip->mime_type,
      'file_size' => $payslip->file_size,
      'notes' => $payslip->notes,
    ];
  }

  private function jobPayload(EmployeeJob $job): array
  {
    return [
      'id' => $job->id,
      'title' => $job->title,
      'description' => $job->description,
      'customer_name' => $job->customer_name,
      'customer_phone' => $job->customer_phone,
      'site_address' => $job->site_address,
      'site_postcode' => $job->site_postcode,
      'scheduled_date' => optional($job->scheduled_date)->toDateString(),
      'scheduled_start_time' => $job->scheduled_start_time ? substr($job->scheduled_start_time, 0, 5) : null,
      'expected_duration_minutes' => $job->expected_duration_minutes,
      'started_at' => optional($job->started_at)->toIso8601String(),
      'finished_at' => optional($job->finished_at)->toIso8601String(),
      'actual_duration_minutes' => $job->actualDurationMinutes(),
      'duration_difference_minutes' => $job->durationDifferenceMinutes(),
      'priority' => $job->priority,
      'status' => $job->status,
      'internal_notes' => $job->internal_notes,
    ];
  }

  private function shiftPayload(EmployeeShift $shift): array
  {
    $clockIn = $shift->attendances->where('clock_type', 'in')->sortByDesc('server_timestamp')->first();
    $clockOut = $shift->attendances->where('clock_type', 'out')->sortByDesc('server_timestamp')->first();

    return [
      'id' => $shift->id,
      'shift_date' => optional($shift->shift_date)->toDateString(),
      'start_time' => substr($shift->start_time, 0, 5),
      'end_time' => substr($shift->end_time, 0, 5),
      'break_minutes' => $shift->break_minutes,
      'work_location' => $shift->work_location,
      'status' => $shift->status,
      'actual_clock_in' => optional(optional($clockIn)->server_timestamp)->toIso8601String(),
      'actual_clock_out' => optional(optional($clockOut)->server_timestamp)->toIso8601String(),
      'next_clock_action' => $shift->status !== 'scheduled' ? null : (!$clockIn ? 'in' : (!$clockOut ? 'out' : null)),
    ];
  }

  private function attendancePayload(EmployeeAttendance $attendance): array
  {
    return [
      'id' => $attendance->id,
      'shift_id' => $attendance->shift_id,
      'job_id' => $attendance->job_id,
      'clock_type' => $attendance->clock_type,
      'server_timestamp' => optional($attendance->server_timestamp)->toIso8601String(),
      'device_timestamp' => optional($attendance->device_timestamp)->toIso8601String(),
      'latitude' => $attendance->latitude,
      'longitude' => $attendance->longitude,
      'accuracy_meters' => $attendance->accuracy_meters,
      'expected_location' => $attendance->expected_location,
      'distance_from_site_meters' => $attendance->distance_from_site_meters,
      'within_geofence' => $attendance->within_geofence,
      'device_platform' => $attendance->device_platform,
      'shift_date' => optional(optional($attendance->shift)->shift_date)->toDateString(),
    ];
  }
}
