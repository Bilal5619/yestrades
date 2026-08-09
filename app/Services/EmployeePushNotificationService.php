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
}