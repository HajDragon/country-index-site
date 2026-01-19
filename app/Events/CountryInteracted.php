<?php

namespace App\Events;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CountryInteracted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Country $country,
        public string $interactionType,
        public ?User $user = null,
        public ?string $sessionId = null
    ) {}
}
