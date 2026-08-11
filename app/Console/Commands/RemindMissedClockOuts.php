<?php

namespace App\Console\Commands;

use App\Models\Company\EmployeeJob;
use App\Models\Company\EmployeeShift;
use App\Services\EmployeePushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RemindMissedClockOuts extends Command
{
    protected $signature = 'attendance:remind-missing-clockouts';

    protected $description =
        'Send a push reminder to employees who clocked in but forgot to clock out (office shifts) or finish a job (field engineers)';

    // How long past the expected end time to wait before nagging, so a
    // reminder doesn't fire the moment a shift/job technically ends.
    private const GRACE_MINUTES = 15;

    public function handle(EmployeePushNotificationService $notifications)
    {
        $shiftCount = $this->remindMissedShiftClockOuts($notifications);
        $jobCount = $this->remindMissedJobFinishes($notifications);

        $this->info("Sent {$shiftCount} missed clock-out reminder(s) and {$jobCount} missed job-finish reminder(s).");

        return Command::SUCCESS;
    }

    private function remindMissedShiftClockOuts(EmployeePushNotificationService $notifications): int
    {
        $sent = 0;

        $shifts = EmployeeShift::query()
            ->with(['employee', 'attendances'])
            ->where('status', 'scheduled')
            ->whereNull('missed_clockout_reminder_sent_at')
            ->whereDate('shift_date', today())
            ->get();

        foreach ($shifts as $shift) {
            $clockIn = $shift->attendances->where('clock_type', 'in')->sortByDesc('server_timestamp')->first();
            $clockOut = $shift->attendances->where('clock_type', 'out')->first();

            if (!$clockIn || $clockOut) {
                continue;
            }

            $expectedEnd = Carbon::parse($shift->shift_date->toDateString() . ' ' . $shift->end_time)
                ->addMinutes(self::GRACE_MINUTES);

            if (Carbon::now()->lt($expectedEnd) || !$shift->employee) {
                continue;
            }

            $notifications->sendMissedClockOutReminder($shift->employee, $shift);
            $shift->update(['missed_clockout_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }

    private function remindMissedJobFinishes(EmployeePushNotificationService $notifications): int
    {
        $sent = 0;

        $jobs = EmployeeJob::query()
            ->with('employee')
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->whereNull('missed_finish_reminder_sent_at')
            ->get();

        foreach ($jobs as $job) {
            $expectedFinish = $job->started_at->copy()
                ->addMinutes((int) ($job->expected_duration_minutes ?? 60))
                ->addMinutes(self::GRACE_MINUTES);

            if (Carbon::now()->lt($expectedFinish) || !$job->employee) {
                continue;
            }

            $notifications->sendMissedJobFinishReminder($job->employee, $job);
            $job->update(['missed_finish_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }
}
