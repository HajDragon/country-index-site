<?php

namespace App\Livewire;

use App\Models\Country;
use App\Models\UserFavorite;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\View\View;

use App\Actions\FetchWeatherData;

class CountryCard extends Component
{
    public string $countryCode;

    /**
     * @var array{
     *   temperature?: float,
     *   humidity?: int,
     *   windSpeed?: float,
     *   weatherCode?: int,
     *   description?: string,
     *   icon?: string
     * }
     */
    public array $weatherData = [];

    public ?Country $country = null;

    public bool $isFavorite = false;

    public function mount(string $countryCode): void
    {
        $this->countryCode = $countryCode;
        $this->country = Country::where('Code', $countryCode)->with(['cities', 'capitalCity'])->first();
        $this->checkIfFavorite();
        $this->loadWeatherData();
    }

    public function checkIfFavorite(): void
    {
        if (Auth::check()) {
            $this->isFavorite = UserFavorite::where('user_id', Auth::id())
                ->where('country_code', $this->countryCode)
                ->exists();
        }
    }

    public function toggleFavorite(): void
    {
        if (! Auth::check()) {
            return;
        }

        $favorite = UserFavorite::where('user_id', Auth::id())
            ->where('country_code', $this->countryCode)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $this->isFavorite = false;
        } else {
            UserFavorite::create([
                'user_id' => Auth::id(),
                'country_code' => $this->countryCode,
            ]);
            $this->isFavorite = true;
        }

        $this->dispatch('favoriteToggled');
    }


    public function loadWeatherData(): void
    {
        if (! $this->country || $this->country->latitude === null || $this->country->longitude === null) {
            $this->weatherData = [];

            return;
        }

        $fetchWeatherData = new FetchWeatherData();
        $this->weatherData = $fetchWeatherData->execute(
            (float) $this->country->latitude,
            (float) $this->country->longitude
        ) ?? [];
    }

    public function render(): View
    {
        return view('livewire.country-card');
    }

    public function goCompare(): void
    {
        $this->redirect(route('countries.compare', ['codes' => $this->countryCode]));
    }
}
