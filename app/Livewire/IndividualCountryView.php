<?php

namespace App\Livewire;

use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;

class IndividualCountryView extends Component
{
    use WithPagination;

    public string $countryCode;

    public ?Country $country = null;

    public function mount(string $countryCode): void
    {
        $this->countryCode = $countryCode;
        $this->country = Country::with(['capitalCity', 'languages'])
            ->where('Code', $countryCode)
            ->firstOrFail();
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
