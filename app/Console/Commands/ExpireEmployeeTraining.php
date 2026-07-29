<?php

namespace App\Console\Commands;

use App\Models\Company\EmployeeTrainingRecord;
use Illuminate\Console\Command;

class ExpireEmployeeTraining extends Command
{
    protected $signature = 'training:expire';

    protected $description =
        'Mark completed employee training as expired when its expiry date has passed';

    public function handle()
    {
        $expiredCount = EmployeeTrainingRecord::query()
            ->where('status', 'completed')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today())
            ->update([
                'status' => 'expired',
                'updated_at' => now(),
            ]);

        $this->info(
            "{$expiredCount} training record(s) marked as expired."
        );

        return Command::SUCCESS;
    }
}