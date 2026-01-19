<?php

use Livewire\Volt\Component;
use App\Services\AnalyticsService;
use App\Traits\HasHomeNavigation;
use Livewire\Attributes\Computed;

new class extends Component {
    use HasHomeNavigation;

    public string $period = '30days';

    #[Computed]
    public function analyticsService(): AnalyticsService
    {
        return app(AnalyticsService::class);
    }

    #[Computed]
    public function mostSearched()
    {
        return $this->analyticsService()->getMostSearchedCountries(10, $this->period);
    }

    #[Computed]
    public function trending()
    {
        return $this->analyticsService()->getTrendingCountries(10);
    }

    #[Computed]
    public function mostCompared()
    {
        return $this->analyticsService()->getMostComparedCountries(10);
    }

    #[Computed]
    public function regionalInsights()
    {
        return $this->analyticsService()->getRegionalInsights();
    }

    #[Computed]
    public function popularComparisons()
    {
        return $this->analyticsService()->getPopularComparisons(10);
    }

    #[Computed]
    public function overallStats()
    {
        return $this->analyticsService()->getOverallStatistics();
    }

    #[Computed]
    public function userVisits()
    {
        if (!auth()->check()) {
            return collect();
        }
        return $this->analyticsService()->getUserCountryVisits(auth()->user());
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        unset($this->mostSearched);
    }
}; ?>

<div class="min-h-screen bg-white dark:bg-gray-900 dark:text-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <flux:heading size="2xl" level="1">Analytics Dashboard</flux:heading>
                <p class="mt-2 text-gray-600 dark:text-gray-400">User interactions and country trends</p>
            </div>
            <div class="flex items-center gap-3">
                <livewire:actions.dark-mode-toggle />
                <flux:button variant="ghost" icon="arrow-left" size="sm" wire:click="goHome">
                    Back
                </flux:button>
            </div>
        </div>

        {{-- Overall Statistics --}}
        <div class="mb-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Interactions</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->overallStats['total_interactions']) }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Views</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->overallStats['total_views']) }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Searches</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->overallStats['total_searches']) }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Unique Countries</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->overallStats['unique_countries_viewed']) }}</div>
            </div>
        </div>

        {{-- Period Selector --}}
        <div class="mb-6 flex gap-2">
            <flux:button
                size="sm"
                :variant="$period === '7days' ? 'primary' : 'ghost'"
                wire:click="setPeriod('7days')">
                Last 7 Days
            </flux:button>
            <flux:button
                size="sm"
                :variant="$period === '30days' ? 'primary' : 'ghost'"
                wire:click="setPeriod('30days')">
                Last 30 Days
            </flux:button>
            <flux:button
                size="sm"
                :variant="$period === '90days' ? 'primary' : 'ghost'"
                wire:click="setPeriod('90days')">
                Last 90 Days
            </flux:button>
            <flux:button
                size="sm"
                :variant="$period === 'all' ? 'primary' : 'ghost'"
                wire:click="setPeriod('all')">
                All Time
            </flux:button>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Most Searched Countries --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <flux:heading size="lg" class="mb-4">Most Searched Countries</flux:heading>
                <div class="space-y-3">
                    @forelse($this->mostSearched as $item)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $item['country']->Code2 ? \Str::upper($item['country']->Code2) : '🌍' }}</span>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item['country']->Name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item['country']->Region }}</div>
                                </div>
                            </div>
                            <flux:badge variant="primary">{{ number_format($item['count']) }}</flux:badge>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">No search data available</p>
                    @endforelse
                </div>
            </div>

            {{-- Trending Countries --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <flux:heading size="lg" class="mb-4">Trending Countries</flux:heading>
                <div class="space-y-3">
                    @forelse($this->trending as $item)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $item['country']->Code2 ? \Str::upper($item['country']->Code2) : '🌍' }}</span>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item['country']->Name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($item['recent_count']) }} views
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <flux:badge :variant="$item['percentage_change'] > 0 ? 'success' : 'danger'">
                                    {{ $item['percentage_change'] > 0 ? '↑' : '↓' }} {{ abs($item['percentage_change']) }}%
                                </flux:badge>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">No trending data available</p>
                    @endforelse
                </div>
            </div>

            {{-- Most Compared Countries --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <flux:heading size="lg" class="mb-4">Most Compared Countries</flux:heading>
                <div class="space-y-3">
                    @forelse($this->mostCompared as $item)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $item['country']->Code2 ? \Str::upper($item['country']->Code2) : '🌍' }}</span>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item['country']->Name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item['country']->Region }}</div>
                                </div>
                            </div>
                            <flux:badge variant="primary">{{ number_format($item['count']) }}</flux:badge>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">No comparison data available</p>
                    @endforelse
                </div>
            </div>

            {{-- Popular Comparison Pairs --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <flux:heading size="lg" class="mb-4">Popular Comparisons</flux:heading>
                <div class="space-y-3">
                    @forelse($this->popularComparisons as $item)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item['country1']->Name }} <span class="text-gray-400">vs</span> {{ $item['country2']->Name }}
                                </div>
                            </div>
                            <flux:badge variant="primary">{{ number_format($item['count']) }}</flux:badge>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">No comparison pairs data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Regional Insights --}}
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <flux:heading size="lg" class="mb-4">Regional Insights</flux:heading>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="pb-3 font-medium">Continent</th>
                            <th class="pb-3 font-medium">Total Interactions</th>
                            <th class="pb-3 font-medium">Unique Countries</th>
                            <th class="pb-3 font-medium">Unique Users</th>
                            <th class="pb-3 font-medium">Avg per Country</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->regionalInsights as $insight)
                            <tr class="text-gray-900 dark:text-gray-100">
                                <td class="py-3 font-medium">{{ $insight['continent'] }}</td>
                                <td class="py-3">{{ number_format($insight['total_interactions']) }}</td>
                                <td class="py-3">{{ number_format($insight['unique_countries']) }}</td>
                                <td class="py-3">{{ number_format($insight['unique_users']) }}</td>
                                <td class="py-3">{{ number_format($insight['avg_interactions_per_country'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500 dark:text-gray-400">No regional data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- User Statistics (if authenticated) --}}
        @auth
            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <flux:heading size="lg" class="mb-4">Your Country Visits</flux:heading>
                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($this->userVisits as $visit)
                        <div class="rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $visit['country']->Code2 ? \Str::upper($visit['country']->Code2) : '🌍' }}</span>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $visit['country']->Name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $visit['visit_count'] }} visit{{ $visit['visit_count'] > 1 ? 's' : '' }} • {{ $visit['last_visited']->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-center text-gray-500 dark:text-gray-400">You haven't visited any countries yet</p>
                    @endforelse
                </div>
            </div>
        @endauth
    </div>
</div>
