<?php

namespace App\Livewire;

use App\Models\Country;
use App\Models\UserFavorite;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CountryCard extends Component
{
    public $countryCode;

    public $country;

    public $isFavorite = false;

    public function mount($countryCode)
    {
        $this->countryCode = $countryCode;
        $this->country = Country::where('Code', $countryCode)->with(['cities', 'capitalCity'])->first();
        $this->checkIfFavorite();
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

    public function render()
    {
        return view('livewire.country-card');
    }

    public function goCompare()
    {
        $this->redirect(route('countries.compare', ['codes' => $this->countryCode]));
    }
}
