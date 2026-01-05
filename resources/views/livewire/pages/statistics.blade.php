<?php

use Livewire\Volt\Component;
use App\Models\Country;
use Illuminate\Support\Facades\Redirect;
use App\Traits\HasHomeNavigation;

new class extends Component {
    use HasHomeNavigation;

    #[\Livewire\Attributes\Computed]
    public function statistics()
    {
        $totalCountries = Country::count();
        $totalPopulation = Country::sum('Population') ?? 0;
        $averagePopulation = $totalCountries > 0 ? (int)($totalPopulation / $totalCountries) : 0;

        // Average life expectancy (excluding nulls)
        $averageLifeExpectancy = (float)(Country::whereNotNull('LifeExpectancy')->avg('LifeExpectancy') ?? 0);

        // Highest and lowest population countries
        $highestPopulationCountry = Country::orderBy('Population', 'desc')->first();
        $highestLifeExpectancyCountry = Country::whereNotNull('LifeExpectancy')->orderBy('LifeExpectancy', 'desc')->first();

        // Continent statistics
        $continentStats = Country::selectRaw('
            Continent,
            COUNT(*) as country_count,
            SUM(Population) as total_population,
            AVG(Population) as avg_population,
            AVG(LifeExpectancy) as avg_life_expectancy,
            MAX(Population) as max_population
        ')
            ->groupBy('Continent')
            ->orderBy('Continent')
            ->get()
            ->map(fn ($stat) => [
                'continent' => $stat->Continent,
                'countryCount' => $stat->country_count,
                'totalPopulation' => $stat->total_population ?? 0,
                'avgPopulation' => (int)($stat->avg_population ?? 0),
                'avgLifeExpectancy' => (float)($stat->avg_life_expectancy ?? 0),
                'maxPopulation' => $stat->max_population ?? 0,
            ])
            ->toArray();

        // Top 10 countries by population
        $topCountriesByPopulation = Country::orderBy('Population', 'desc')->limit(10)->get()->toArray();

        return [
            'totalCountries' => $totalCountries,
            'totalPopulation' => $totalPopulation,
            'averagePopulation' => $averagePopulation,
            'averageLifeExpectancy' => $averageLifeExpectancy,
            'highestPopulationCountry' => $highestPopulationCountry,
            'highestLifeExpectancyCountry' => $highestLifeExpectancyCountry,
            'continentStats' => $continentStats,
            'topCountriesByPopulation' => $topCountriesByPopulation,
        ];
    }
}; ?>

<div class="min-h-screen bg-white dark:bg-gray-900 dark:text-white ">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <flux:heading size="2xl" level="1">World Statistics</flux:heading>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Global country data insights and analytics</p>
            </div>
            <div class="flex items-center gap-3">
                <livewire:actions.dark-mode-toggle />
                <flux:button variant="ghost" icon="arrow-left" icon:variant="outline" size="sm"
                wire:click="goHome"
                class="rounded bg-blue-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-blue-700"
                onclick="event.stopPropagation();">
                <div wire:loading.remove>Back</div>
            <div wire:loading>Going back...</div>

        </flux:button>
            </div>
        </div>

        {{-- Main Stats Grid --}}
        <div class="mb-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            {{-- Total Countries --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Countries</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $this->statistics['totalCountries'] }}</div>
            </div>

            {{-- Total Population --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">World Population</div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->statistics['totalPopulation'], 0) }}</div>
            </div>

            {{-- Average Population --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Population</div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->statistics['averagePopulation'], 0) }}</div>
            </div>

            {{-- Avg Life Expectancy --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Life Expectancy</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->statistics['averageLifeExpectancy'], 1) }} <span class="text-sm">years</span></div>
            </div>
        </div>

        {{-- Key Countries --}}
        <div class="mb-8 grid gap-6 md:grid-cols-2">
            {{-- Highest Population --}}
            @if($this->statistics['highestPopulationCountry'])
            <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-semibold">Largest by Population</h3>
                <div class="flex items-center gap-4">
                    <img src="https://flagsapi.com/{{ $this->statistics['highestPopulationCountry']['Code2'] }}/flat/64.png" alt="{{ $this->statistics['highestPopulationCountry']['Name'] }}" class="h-12 w-16 rounded">
                    <div>
                        <p class="font-semibold">{{ $this->statistics['highestPopulationCountry']['Name'] }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ number_format($this->statistics['highestPopulationCountry']['Population']) }} people</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Highest Life Expectancy --}}
            @if($this->statistics['highestLifeExpectancyCountry'])
            <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-semibold">Highest Life Expectancy</h3>
                <div class="flex items-center gap-4">
                    <img src="https://flagsapi.com/{{ $this->statistics['highestLifeExpectancyCountry']['Code2'] }}/flat/64.png" alt="{{ $this->statistics['highestLifeExpectancyCountry']['Name'] }}" class="h-12 w-16 rounded">
                    <div>
                        <p class="font-semibold">{{ $this->statistics['highestLifeExpectancyCountry']['Name'] }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ number_format($this->statistics['highestLifeExpectancyCountry']['LifeExpectancy'], 1) }} years</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Continent Statistics --}}
        <div class="mb-8">
            <flux:heading size="lg" level="2" class="mb-4">Statistics by Continent</flux:heading>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Continent</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Countries</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Total Population</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Avg Population</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Avg Life Expectancy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->statistics['continentStats'] as $stat)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 text-sm font-medium">{{ $stat['continent'] }}</td>
                            <td class="px-6 py-4 text-sm">{{ $stat['countryCount'] }}</td>
                            <td class="px-6 py-4 text-sm">{{ number_format($stat['totalPopulation'], 0) }}</td>
                            <td class="px-6 py-4 text-sm">{{ number_format($stat['avgPopulation'], 0) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($stat['avgLifeExpectancy'] > 0)
                                    {{ number_format($stat['avgLifeExpectancy'], 1) }} years
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top 10 Countries by Population --}}
        <div>
            <flux:heading size="lg" level="2" class="mb-4">Top 10 Countries by Population</flux:heading>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                @foreach($this->statistics['topCountriesByPopulation'] as $index => $country)
                <a href="{{ route('country.view', $country['Code']) }}" wire:navigate class="group rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-blue-900/20">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold dark:bg-gray-700">{{ $index + 1 }}</span>
                        <img src="https://flagsapi.com/{{ $country['Code2'] }}/flat/32.png" alt="{{ $country['Name'] }}" class="h-6 w-8 rounded">
                    </div>
                    <p class="font-semibold group-hover:text-blue-600">{{ $country['Name'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($country['Population']) }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
