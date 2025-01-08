<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNews;
use App\Services\NewsFetcher;
use Illuminate\Console\Command;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ProcessNews::dispatch();
        $this->info("Process News Job Dispatched!");
    }
}
