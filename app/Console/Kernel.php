<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel

{
    /**
     *
     * @var array
     */

    protected $commands = [
        Commands\FetchArticles::class
    ];


    /**
     *
     * @param
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
        // Trigger transaction check...
        $schedule->command('app:fetch-articles')->everyMinute();
    }
}
