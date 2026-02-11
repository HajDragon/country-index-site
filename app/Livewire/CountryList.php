<?php

namespace App\Livewire;

use App\Events\CountryInteracted;
use App\Exports\CountriesExport;
use App\Models\Country;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class CountryList extends Component
{
    public $search = '';

    public $searchTerm = '';

    public $sortBy = 'name_asc';

    public $showFavoritesOnly = false;

    // Infinite scroll properties
    public int $perPage = 12;

    public int $page = 1;

    public bool $hasMore = true;

    public array $loadedCountryCodes = [];

    // Filter properties
    public $selectedContinents = [];

    public $selectedRegions = [];

    public $populationMin = 0;

    public $populationMax = 0;

    public $lifeExpectancyMin = 0;

    public $lifeExpectancyMax = 100;

    public $showFilters = false;

    public function mount()
    {
        // Set default population max to 2 billion
        $this->populationMax = 2000000000;
    }

    public function performSearch(): void
    {
        $this->search = $this->searchTerm;
        $this->resetScroll();

        // Track search interaction
        if (! empty($this->searchTerm)) {
            // Get first matching country for tracking purposes
            $country = Country::where('Name', 'like', "%{$this->searchTerm}%")
                ->orWhere('Code', 'like', "%{$this->searchTerm}%")
                ->first();

            if ($country) {
                CountryInteracted::dispatch($country, 'search', auth()->user());
            }
        }
    }

    public function updatedSearchTerm(): void
    {
        $this->search = $this->searchTerm;
        $this->resetScroll();
    }

    public function updatedSearch(): void
    {
        $this->resetScroll();
    }

    public function updatedSortBy(): void
    {
        $this->resetScroll();
    }

    public function updatedSelectedContinents(): void
    {
        $this->resetScroll();
    }

    public function updatedSelectedRegions(): void
    {
        $this->resetScroll();
    }

    public function updatedPopulationMin(): void
    {
        $this->resetScroll();
    }

    public function updatedPopulationMax(): void
    {
        $this->resetScroll();
    }

    public function updatedLifeExpectancyMin(): void
    {
        $this->resetScroll();
    }

    public function updatedLifeExpectancyMax(): void
    {
        $this->resetScroll();
    }

    public function updatedShowFavoritesOnly(): void
    {
        $this->resetScroll();
    }

    public function resetScroll(): void
    {
        $this->page = 1;
        $this->hasMore = true;
        $this->loadedCountryCodes = [];
    }

    public function loadMore(): void
    {
        $this->page++;
    }

    public function clearFilters(): void
    {
        $this->selectedContinents = [];
        $this->selectedRegions = [];
        $this->populationMin = 0;
        $this->populationMax = 2000000000;
        $this->lifeExpectancyMin = 0;
        $this->lifeExpectancyMax = 100;
        $this->resetScroll();
    }

    public function render()
    {
        // Cache these queries - they rarely change
        $continents = cache()->remember('continents_list', 3600, fn () => Country::distinct()->pluck('Continent')->sort()
        );
        $regions = cache()->remember('regions_list', 3600, fn () => Country::distinct()->pluck('Region')->sort()
        );

        $isSearching = ! empty($this->search);
        $limit = $this->page * $this->perPage;

        if ($isSearching) {
            // For Scout search - optimized with single query
            $allResults = Country::search($this->search)
                ->query(fn ($query) => $this->applyFilters($query))
                ->get();

            $totalCount = $allResults->count();
            $countries = $allResults->take($limit);
        } else {
            // For regular Eloquent query - optimized with eager loading
            $query = $this->applyFilters(Country::query()->with('capitalCity'));

            // Get count efficiently
            $totalCount = $query->count();

            // Get paginated results
            $countries = $query->take($limit)->get();
        }

        // Track loaded country codes
        $this->loadedCountryCodes = $countries->pluck('Code')->toArray();

        // Update hasMore flag
        $this->hasMore = $totalCount > $limit;

        return view('livewire.country-list', [
            'countries' => $countries,
            'continents' => $continents,
            'regions' => $regions,
            'totalCount' => $totalCount,
        ]);
    }

    protected function applyFilters($query)
    {
        // Apply continent filters
        if (! empty($this->selectedContinents)) {
            $query->whereIn('Continent', $this->selectedContinents);
        }

        // Apply region filters
        if (! empty($this->selectedRegions)) {
            $query->whereIn('Region', $this->selectedRegions);
        }

        // Apply population range filter
        if ($this->populationMin > 0) {
            $query->where('Population', '>=', $this->populationMin);
        }
        if ($this->populationMax > 0) {
            $query->where('Population', '<=', $this->populationMax);
        }

        // Apply life expectancy range filter
        $query->where('LifeExpectancy', '>=', $this->lifeExpectancyMin)
            ->where('LifeExpectancy', '<=', $this->lifeExpectancyMax);

        // Apply favorites filter
        if ($this->showFavoritesOnly && Auth::check()) {
            $favoriteCountryCodes = Auth::user()->favoriteCountries()
                ->pluck('country_code')
                ->toArray();
            $query->whereIn('Code', $favoriteCountryCodes);
        }

        return $this->applySorting($query);
    }

    protected function applySorting($query)
    {
        return match ($this->sortBy) {
            'continent' => $query->orderBy('Continent', 'asc')->orderBy('Name', 'asc'),
            'name_asc' => $query->orderBy('Name', 'asc'),
            'name_desc' => $query->orderBy('Name', 'desc'),
            'population_desc' => $query->orderBy('Population', 'desc'),
            'population_asc' => $query->orderBy('Population', 'asc'),
            default => $query->orderBy('Name', 'asc'),
        };
    }

    public function exportCsv()
    {
        $query = $this->getFilteredCountriesQuery();
        $filename = 'countries-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new CountriesExport($query), $filename);
    }

    public function exportPdf()
    {
        $countries = $this->getFilteredCountriesQuery()->get();
        $pdf = Pdf::loadView('exports.countries-pdf', [
            'countries' => $countries,
            'totalCount' => $countries->count(),
            'exportedAt' => now()->format('F j, Y \a\t g:i A'),
        ]);
        $filename = 'countries-'.now()->format('Y-m-d-His').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    private function getFilteredCountriesQuery()
    {
        $query = Country::query();

        if ($this->search) {
            $searchTerm = "%{$this->search}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('Name', 'like', $searchTerm)
                    ->orWhere('Region', 'like', $searchTerm)
                    ->orWhereHas('capitalCity', function ($q) use ($searchTerm) {
                        $q->where('Name', 'like', $searchTerm);
                    });
            });
        }

        if (! empty($this->selectedContinents)) {
            $query->whereIn('Continent', $this->selectedContinents);
        }

        if (! empty($this->selectedRegions)) {
            $query->whereIn('Region', $this->selectedRegions);
        }

        if ($this->populationMin > 0) {
            $query->where('Population', '>=', $this->populationMin);
        }

        if ($this->populationMax > 0) {
            $query->where('Population', '<=', $this->populationMax);
        }

        if ($this->lifeExpectancyMin > 0) {
            $query->where('LifeExpectancy', '>=', $this->lifeExpectancyMin);
        }

        if ($this->lifeExpectancyMax < 100) {
            $query->where('LifeExpectancy', '<=', $this->lifeExpectancyMax);
        }

        if ($this->showFavoritesOnly && Auth::check()) {
            $favoriteCountryCodes = Auth::user()->favoriteCountries()
                ->pluck('country_code')
                ->toArray();
            $query->whereIn('Code', $favoriteCountryCodes);
        }

        return $query;
    }
}
