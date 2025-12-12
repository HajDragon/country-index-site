<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Country;
use App\Models\City;

class RussiaCard extends Component
{
    public $Russia;

    public function mount()
    {
        $this->Russia = Country::where('Code', 'RUS')->with('cities')->first();
    }

    public function render()
    {
        return view('livewire.russia-card');
    }
}
