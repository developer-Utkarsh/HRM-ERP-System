<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;


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
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            // Array of URLs to hit
            $urls = [
                'https://hrm.utkarshupdates.com/index.php/api/topicSelectionReminder?before_class=234',
                'https://hrm.utkarshupdates.com/index.php/api/topicSelectionReminder?after_class=234',
                'https://hrm.utkarshupdates.com/index.php/npf-callback',
            ];

            foreach($urls as $url){
             $response = Http::get($url);
            }
        })->everyFifteenMinutes();
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
