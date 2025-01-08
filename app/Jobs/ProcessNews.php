<?php

namespace App\Jobs;

use App\Services\NewsFetcher;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sources = ['news_api', 'new_york_times', 'the_guardian'];
        foreach ($sources as $source) {
            (new NewsFetcher())->fetchArticlesFromSource($source);
        }
    }
}
