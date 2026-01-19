<?php

namespace App\Livewire;

use App\Events\CountryInteracted;
use App\Models\Country;
use App\Traits\HasHomeNavigation;
use Livewire\Component;
use Livewire\WithPagination;

class IndividualCountryView extends Component
{
    use HasHomeNavigation;
    use WithPagination;

    public string $countryCode;

    public ?Country $country = null;

    public function mount(string $countryCode): void
    {
        $this->countryCode = $countryCode;
        $this->country = Country::with(['capitalCity', 'languages'])
            ->where('Code', $countryCode)
            ->firstOrFail();

        // Track country view
        CountryInteracted::dispatch($this->country, 'view', auth()->user());
    }

    public function render()
    {
        $cities = $this->country->cities()
            ->orderBy('Population', 'desc')
            ->paginate(12);

        return view('livewire.individual-country-view', [
            'cities' => $cities,
        ]);
    }
}
