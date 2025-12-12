<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Country;
use App\Models\City;

class ChinaCard extends Component
{
    public $China;

    public function mount()
    {
        $this->China = Country::where('Code', 'CHN')->with('cities')->first();
    }

    public function render()
    {
        return view('livewire.china-card');
    }
}
