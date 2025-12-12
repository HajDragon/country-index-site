<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Country;

class CountryList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.country-list', [
            'countries' => Country::paginate(12)
        ]);
    }
}
