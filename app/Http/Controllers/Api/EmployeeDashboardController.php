<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company\Announcement;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeDashboardController extends Controller
{
  public function index(Request $request): JsonResponse
  {
    /** @var Employee $employee */
    $employee = $request->user();
    $today = now()->toDateString();

    $todayShift = $employee->shifts()
      ->where('shift_date', $today)
      ->orderBy('start_time')
      ->first();

    $nextJob = $employee->jobs()
      ->where('scheduled_date', '>=', $today)
      ->whereNotIn('status', ['completed', 'cancelled'])
      ->orderBy('scheduled_date')
      ->orderBy('scheduled_start_time')
      ->first();

    $todayJobsCount = $employee->jobs()
      ->where('scheduled_date', $today)
      ->whereNotIn('status', ['cancelled'])
      ->count();

    $currentYear = now()->year;
    $allowance = $employee->currentHolidayAllowance;
    $pendingDays = $employee->holidays()
      ->where('status', 'pending')
      ->whereYear('start_date', $currentYear)
      ->sum('total_days');

    $trainingDueCount = $employee->trainingRecords()
      ->where(function ($query) {
        $query->whereIn('status', ['not_started', 'scheduled', 'in_progress', 'expired'])
          ->orWhere(function ($subQuery) {
            $subQuery->where('status', 'completed')
              ->whereNotNull('expiry_date')
              ->whereDate('expiry_date', '>=', today())
              ->whereDate('expiry_date', '<=', today()->addDays(30));
          });
      })
      ->count();

    $readAnnouncementIds = $employee->announcementReads()->pluck('announcement_id');
    $publishedAnnouncements = Announcement::query()
      ->whereNotNull('published_at')
      ->where('published_at', '<=', now())
      ->orderByDesc('published_at');

    $unreadAnnouncementsCount = (clone $publishedAnnouncements)
      ->whereNotIn('id', $readAnnouncementIds)
      ->count();

    $latestAnnouncement = (clone $publishedAnnouncements)->first();

    $now = Carbon::now();
    $hoursSummary = [
      'today_minutes' => $this->workedMinutesForRange($employee, $now->copy()->startOfDay(), $now->copy()->endOfDay()),
      'week_minutes' => $this->workedMinutesForRange($employee, $now->copy()->startOfWeek(), $now->copy()->endOfWeek()),
      'month_minutes' => $this->workedMinutesForRange($employee, $now->copy()->startOfMonth(), $now->copy()->endOfMonth()),
    ];

    return response()->json([
      'success' => true,
      'data' => [
        'employee' => [
          'full_name' => $employee->full_name,
          'job_title' => $employee->job_title,
          'employee_number' => $employee->employee_number,
          'profile_photo' => $employee->profile_photo,
        ],
        'today_shift' => $todayShift ? [
          'start_time' => substr($todayShift->start_time, 0, 5),
          'end_time' => substr($todayShift->end_time, 0, 5),
          'work_location' => $todayShift->work_location,
          'status' => $todayShift->status,
        ] : null,
        'next_job' => $nextJob ? [
          'id' => $nextJob->id,
          'title' => $nextJob->title,
          'scheduled_date' => $nextJob->scheduled_date->toDateString(),
          'scheduled_start_time' => $nextJob->scheduled_start_time ? substr($nextJob->scheduled_start_time, 0, 5) : null,
          'customer_name' => $nextJob->customer_name,
        ] : null,
        'today_jobs_count' => $todayJobsCount,
        'holiday_summary' => [
          'accrued' => $allowance ? round((float) $allowance->total_allowance, 2) : 0,
          'taken' => $allowance ? round((float) $allowance->approved_days_used, 2) : 0,
          'pending' => round((float) $pendingDays, 2),
          'remaining' => $allowance ? round((float) $allowance->remaining_allowance, 2) : 0,
        ],
        'training_due_count' => $trainingDueCount,
        'hours_summary' => $hoursSummary,
        'unread_announcements_count' => $unreadAnnouncementsCount,
        'latest_announcement' => $latestAnnouncement ? [
          'id' => $latestAnnouncement->id,
          'title' => $latestAnnouncement->title,
          'is_urgent' => $latestAnnouncement->is_urgent,
        ] : null,
      ],
    ]);
  }

  // Combined office (shift) + field (job) worked minutes for this employee
  // over a fixed date window. Mirrors Company\TimesheetController's
  // equivalent admin-side calculation.
  private function workedMinutesForRange(Employee $employee, Carbon $start, Carbon $end): int
  {
    $officeMinutes = $employee->shifts()
      ->with('attendances')
      ->whereBetween('shift_date', [$start->toDateString(), $end->toDateString()])
      ->get()
      ->sum(function (EmployeeShift $shift) {
        $clockIn = $shift->attendances->where('clock_type', 'in')->sortByDesc('server_timestamp')->first();
        $clockOut = $shift->attendances->where('clock_type', 'out')->sortByDesc('server_timestamp')->first();
        if (!$clockIn || !$clockOut) {
          return 0;
        }
        return max(0, $clockIn->server_timestamp->diffInMinutes($clockOut->server_timestamp) - $shift->break_minutes);
      });

    $fieldMinutes = $employee->jobs()
      ->whereNotNull('started_at')
      ->whereNotNull('finished_at')
      ->whereBetween('scheduled_date', [$start->toDateString(), $end->toDateString()])
      ->get()
      ->sum(fn ($job) => $job->actualDurationMinutes() ?? 0);

    return (int) ($officeMinutes + $fieldMinutes);
  }
}
