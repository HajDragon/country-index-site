<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Country;
use App\Models\City;

class CountryCard extends Component
{
    public $countryCode;
    public $country;

    public function mount($countryCode)
    {
        $this->countryCode = $countryCode;
        $this->country = Country::where('Code', $countryCode)->with('cities')->first();
    }

    public function render()
    {
        return view('livewire.country-card');
    }
}
