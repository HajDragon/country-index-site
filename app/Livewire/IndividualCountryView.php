<?php

namespace App\Livewire;

use App\Actions\FetchCovidStats;
use App\Actions\FetchExchangeRates;
use App\Events\CountryInteracted;
use App\Models\Country;
use App\Traits\HasHomeNavigation;
use Livewire\Attributes\Computed;
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

    #[Computed]
    public function exchangeRates(): ?array
    {
        if (! $this->country->currency_code) {
            return null;
        }

        return app(FetchExchangeRates::class)->execute($this->country->currency_code);
    }

    #[Computed]
    public function covidStats(): ?array
    {
        return app(FetchCovidStats::class)->execute($this->country->Name);
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
