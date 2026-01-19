<?php

namespace App\Livewire;

use App\Events\CountryInteracted;
use App\Exports\CountriesExport;
use App\Models\Country;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class CountryList extends Component
{
    use WithPagination;

    public $search = '';

    public $searchTerm = '';

    public $sortBy = 'name_asc';

    public $showFavoritesOnly = false;

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
        $this->resetPage();

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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedContinents(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedRegions(): void
    {
        $this->resetPage();
    }

    public function updatedPopulationMin(): void
    {
        $this->resetPage();
    }

    public function updatedPopulationMax(): void
    {
        $this->resetPage();
    }

    public function updatedLifeExpectancyMin(): void
    {
        $this->resetPage();
    }

    public function updatedLifeExpectancyMax(): void
    {
        $this->resetPage();
    }

    public function updatedShowFavoritesOnly(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->selectedContinents = [];
        $this->selectedRegions = [];
        $this->populationMin = 0;
        $this->populationMax = 2000000000;
        $this->lifeExpectancyMin = 0;
        $this->lifeExpectancyMax = 100;
        $this->resetPage();
    }

    public function render()
    {
        $continents = Country::distinct()->pluck('Continent')->sort();
        $regions = Country::distinct()->pluck('Region')->sort();

        $query = $this->search
            ? Country::search($this->search)->query(fn ($query) => $this->applyFilters($query))
            : $this->applyFilters(Country::query());

        $countries = $query->paginate(12);

        return view('livewire.country-list', [
            'countries' => $countries,
            'continents' => $continents,
            'regions' => $regions,
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
