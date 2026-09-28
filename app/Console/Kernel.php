<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
    //
  ];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $schedule
    ->command('training:expire')
    ->dailyAt('00:10')
    ->withoutOverlapping();

    $schedule
    ->command('attendance:remind-missing-clockouts')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

    $schedule
    ->command('tenant-slots:expire')
    ->hourly()
    ->withoutOverlapping();

    // Certificate expiry warnings (30 days before, and once after expiry).
    $schedule
    ->command('certificates:remind')
    ->dailyAt('07:00')
    ->withoutOverlapping();
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
