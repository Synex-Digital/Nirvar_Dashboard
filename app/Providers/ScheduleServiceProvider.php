<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\DailyInputCheckJob;
use App\Jobs\WeeklyResultNotificationJob;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule): void
    {
        $schedule->job(new DailyInputCheckJob)->twiceDaily(10, 20);
        $schedule->job(new WeeklyResultNotificationJob)->weeklyOn(6, '10:00');
    }
}
