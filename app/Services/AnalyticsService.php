<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CountryInteraction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get the most searched countries within a specified period
     */
    public function getMostSearchedCountries(int $limit = 10, string $period = '30days'): Collection
    {
        return Cache::remember("analytics:most-searched:{$period}:{$limit}", now()->addHours(1), function () use ($limit, $period) {
            $date = $this->getPeriodDate($period);

            return CountryInteraction::query()
                ->select('country_id', DB::raw('COUNT(*) as interaction_count'))
                ->where('interaction_type', 'search')
                ->when($date, fn ($query) => $query->where('created_at', '>=', $date))
                ->groupBy('country_id')
                ->orderByDesc('interaction_count')
                ->limit($limit)
                ->with('country')
                ->get()
                ->map(fn ($item) => [
                    'country' => $item->country,
                    'count' => $item->interaction_count,
                ]);
        });
    }

    /**
     * Get trending countries (countries with increasing view velocity)
     */
    public function getTrendingCountries(int $limit = 10): Collection
    {
        return Cache::remember("analytics:trending:{$limit}", now()->addHours(1), function () use ($limit) {
            $recentPeriod = now()->subDays(7);
            $previousPeriod = now()->subDays(14);

            // Get recent views (last 7 days)
            $recentViews = CountryInteraction::query()
                ->select('country_id', DB::raw('COUNT(*) as recent_count'))
                ->whereIn('interaction_type', ['view', 'search'])
                ->where('created_at', '>=', $recentPeriod)
                ->groupBy('country_id')
                ->pluck('recent_count', 'country_id');

            // Get previous views (7-14 days ago)
            $previousViews = CountryInteraction::query()
                ->select('country_id', DB::raw('COUNT(*) as previous_count'))
                ->whereIn('interaction_type', ['view', 'search'])
                ->whereBetween('created_at', [$previousPeriod, $recentPeriod])
                ->groupBy('country_id')
                ->pluck('previous_count', 'country_id');

            // Calculate trending score
            $trending = collect($recentViews)->map(function ($recentCount, $countryId) use ($previousViews) {
                $previousCount = $previousViews->get($countryId, 1);
                $percentageChange = (($recentCount - $previousCount) / $previousCount) * 100;

                return [
                    'country_id' => $countryId,
                    'recent_count' => $recentCount,
                    'previous_count' => $previousCount,
                    'percentage_change' => round($percentageChange, 2),
                    'trending_score' => $recentCount * (1 + ($percentageChange / 100)),
                ];
            })->sortByDesc('trending_score')->take($limit);

            // Load country relationships
            $countryIds = $trending->pluck('country_id');
            $countries = Country::whereIn('Code', $countryIds)->get()->keyBy('Code');

            return $trending->map(fn ($item) => array_merge($item, [
                'country' => $countries->get($item['country_id']),
            ]));
        });
    }

    /**
     * Get the most compared countries
     */
    public function getMostComparedCountries(int $limit = 10): Collection
    {
        return Cache::remember("analytics:most-compared:{$limit}", now()->addHours(1), function () use ($limit) {
            return CountryInteraction::query()
                ->select('country_id', DB::raw('COUNT(*) as comparison_count'))
                ->where('interaction_type', 'compare')
                ->groupBy('country_id')
                ->orderByDesc('comparison_count')
                ->limit($limit)
                ->with('country')
                ->get()
                ->map(fn ($item) => [
                    'country' => $item->country,
                    'count' => $item->comparison_count,
                ]);
        });
    }

    /**
     * Get regional insights (aggregated stats by continent)
     */
    public function getRegionalInsights(): Collection
    {
        return Cache::remember('analytics:regional-insights', now()->addHours(6), function () {
            return CountryInteraction::query()
                ->join('country', 'country_interactions.country_id', '=', 'country.Code')
                ->select(
                    'country.Continent',
                    DB::raw('COUNT(country_interactions.id) as total_interactions'),
                    DB::raw('COUNT(DISTINCT country_interactions.country_id) as unique_countries'),
                    DB::raw('COUNT(DISTINCT country_interactions.user_id) as unique_users')
                )
                ->groupBy('country.Continent')
                ->orderByDesc('total_interactions')
                ->get()
                ->map(fn ($item) => [
                    'continent' => $item->Continent,
                    'total_interactions' => $item->total_interactions,
                    'unique_countries' => $item->unique_countries,
                    'unique_users' => $item->unique_users,
                    'avg_interactions_per_country' => round($item->total_interactions / max($item->unique_countries, 1), 2),
                ]);
        });
    }

    /**
     * Get countries visited by a specific user
     */
    public function getUserCountryVisits(User $user): Collection
    {
        return CountryInteraction::query()
            ->select('country_id', DB::raw('MAX(created_at) as last_visited'), DB::raw('COUNT(*) as visit_count'))
            ->where('user_id', $user->id)
            ->groupBy('country_id')
            ->orderByDesc('last_visited')
            ->with('country')
            ->get()
            ->map(fn ($item) => [
                'country' => $item->country,
                'last_visited' => Carbon::parse($item->last_visited),
                'visit_count' => $item->visit_count,
            ]);
    }

    /**
     * Get popular country comparison pairs
     */
    public function getPopularComparisons(int $limit = 10): Collection
    {
        return Cache::remember("analytics:popular-comparisons:{$limit}", now()->addHours(2), function () use ($limit) {
            // Get comparison sessions and group countries compared together
            return CountryInteraction::query()
                ->where('interaction_type', 'compare')
                ->whereNotNull('session_id')
                ->select('session_id', 'country_id', 'created_at')
                ->orderBy('session_id')
                ->orderBy('created_at')
                ->get()
                ->groupBy('session_id')
                ->map(function ($interactions) {
                    $countries = $interactions->pluck('country_id')->sort()->values();
                    if ($countries->count() >= 2) {
                        return [
                            'country1' => $countries[0],
                            'country2' => $countries[1],
                        ];
                    }

                    return null;
                })
                ->filter()
                ->groupBy(fn ($pair) => $pair['country1'].'|'.$pair['country2'])
                ->map(fn ($pairs, $key) => [
                    'countries' => explode('|', $key),
                    'count' => $pairs->count(),
                ])
                ->sortByDesc('count')
                ->take($limit)
                ->values()
                ->map(function ($item) {
                    $countries = Country::whereIn('Code', $item['countries'])->get()->keyBy('Code');

                    return [
                        'country1' => $countries->get($item['countries'][0]),
                        'country2' => $countries->get($item['countries'][1]),
                        'count' => $item['count'],
                    ];
                });
        });
    }

    /**
     * Get overall statistics
     */
    public function getOverallStatistics(): array
    {
        return Cache::remember('analytics:overall-stats', now()->addHours(1), function () {
            return [
                'total_interactions' => CountryInteraction::count(),
                'total_views' => CountryInteraction::where('interaction_type', 'view')->count(),
                'total_searches' => CountryInteraction::where('interaction_type', 'search')->count(),
                'total_comparisons' => CountryInteraction::where('interaction_type', 'compare')->count(),
                'total_favorites' => CountryInteraction::where('interaction_type', 'favorite')->count(),
                'unique_countries_viewed' => CountryInteraction::distinct('country_id')->count(),
                'unique_users' => CountryInteraction::whereNotNull('user_id')->distinct('user_id')->count(),
            ];
        });
    }

    /**
     * Clear all analytics caches
     */
    public function clearCache(): void
    {
        $patterns = [
            'analytics:most-searched:*',
            'analytics:trending:*',
            'analytics:most-compared:*',
            'analytics:regional-insights',
            'analytics:popular-comparisons:*',
            'analytics:overall-stats',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get the date for a given period
     */
    protected function getPeriodDate(string $period): ?Carbon
    {
        return match ($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'all' => null,
            default => now()->subDays(30),
        };
    }
}
