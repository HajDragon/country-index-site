<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Country;
use App\Models\City;

class UsaCard extends Component
{
    public $usa;

    public function mount()
    {
        $this->usa = Country::where('Code', 'USA')->with('cities')->first();
    }

    public function render()
    {
        return view('livewire.usa-card');
    }
}
