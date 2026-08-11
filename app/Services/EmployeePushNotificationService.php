<?php

namespace App\Services;

use App\Models\Company\Employee;
use App\Models\Company\EmployeeJob;
use Illuminate\Support\Facades\Http;

class EmployeePushNotificationService
{
    public function sendJobAssigned(
        Employee $employee,
        EmployeeJob $job
    ): void {
        $tokens = $employee->pushTokens()
            ->pluck('expo_push_token')
            ->filter();

        foreach ($tokens as $token) {
            Http::post(
                'https://exp.host/--/api/v2/push/send',
                [
                    'to' => $token,
                    'title' => 'New Job Assigned',
                    'body' => trim(
                        $job->title .
                        ' · ' .
                        $job->scheduled_date .
                        ($job->scheduled_start_time
                            ? ' ' . $job->scheduled_start_time
                            : '')
                    ),
                    'sound' => 'job_ringtone.wav',
                    'priority' => 'high',
                    'channelId' => 'job-alerts',
                    'data' => [
                        'type' => 'job_alert',
                        'alert_type' => 'assigned',
                        'job_id' => $job->id,
                    ],
                ]
            );
        }
    }

    public function sendMissedClockOutReminder(
        Employee $employee,
        \App\Models\Company\EmployeeShift $shift
    ): void {
        $tokens = $employee->pushTokens()
            ->pluck('expo_push_token')
            ->filter();

        foreach ($tokens as $token) {
            Http::post(
                'https://exp.host/--/api/v2/push/send',
                [
                    'to' => $token,
                    'title' => "Don't forget to clock out",
                    'body' => 'Your shift ended at ' . substr($shift->end_time, 0, 5) . ' but you\'re still clocked in.',
                    'sound' => 'default',
                    'priority' => 'high',
                    'channelId' => 'job-alerts',
                    'data' => [
                        'type' => 'attendance_reminder',
                        'alert_type' => 'missed_clock_out',
                        'shift_id' => $shift->id,
                    ],
                ]
            );
        }
    }

    public function sendMissedJobFinishReminder(
        Employee $employee,
        EmployeeJob $job
    ): void {
        $tokens = $employee->pushTokens()
            ->pluck('expo_push_token')
            ->filter();

        foreach ($tokens as $token) {
            Http::post(
                'https://exp.host/--/api/v2/push/send',
                [
                    'to' => $token,
                    'title' => "Don't forget to finish this job",
                    'body' => trim($job->title) . ' is still marked as in progress. Tap Finish Job once you\'re done.',
                    'sound' => 'default',
                    'priority' => 'high',
                    'channelId' => 'job-alerts',
                    'data' => [
                        'type' => 'attendance_reminder',
                        'alert_type' => 'missed_job_finish',
                        'job_id' => $job->id,
                    ],
                ]
            );
        }
    }
}