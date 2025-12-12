<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Country;

class CountryList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name_asc';


    public function getUserName(): ?string
    {
        return Auth::user()?->name;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->search
            ? Country::search($this->search)->query(fn($query) => $this->applySorting($query))
            : $this->applySorting(Country::query());

        $countries = $query->paginate(12);

        return view('livewire.country-list', [
            'countries' => $countries
        ]);

    }

    protected function applySorting($query)
    {
        return match($this->sortBy) {
            'Continent' => $query->orderBy('Region', 'asc')->orderBy('Name', 'asc'),
            'name_asc' => $query->orderBy('Name', 'asc'),
            'name_desc' => $query->orderBy('Name', 'desc'),
            'population_desc' => $query->orderBy('Population', 'desc'),
            'population_asc' => $query->orderBy('Population', 'asc'),
            default => $query->orderBy('Name', 'asc'),
        };
    }
}
