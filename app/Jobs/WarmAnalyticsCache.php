<?php

namespace App\Jobs;

use App\Services\AnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WarmAnalyticsCache implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(AnalyticsService $analytics): void
    {
        // Warm up the cache for all analytics queries
        $analytics->getOverallStatistics();
        $analytics->getMostSearchedCountries(10, '7days');
        $analytics->getMostSearchedCountries(10, '30days');
        $analytics->getMostSearchedCountries(10, '90days');
        $analytics->getMostSearchedCountries(10, 'all');
        $analytics->getTrendingCountries(10);
        $analytics->getMostComparedCountries(10);
        $analytics->getRegionalInsights();
        $analytics->getPopularComparisons(10);
    }
}
