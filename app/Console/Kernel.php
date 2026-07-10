<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Jalankan otomasi pembaruan tahun ajaran setiap hari
        $schedule->command('app:update-academic-year')->daily();

        // Jadwal backup database (tanpa file) seminggu sekali tiap hari Senin jam 01:00
        $schedule->command('backup:run --only-db')->weeklyOn(1, '01:00');
        
        // Membersihkan backup lama
        $schedule->command('backup:clean')->weeklyOn(1, '01:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
