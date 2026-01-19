<?php

namespace App\Listeners;

use App\Events\CountryInteracted;
use App\Models\CountryInteraction;

class TrackCountryInteraction
{
    /**
     * Handle the event.
     */
    public function handle(CountryInteracted $event): void
    {
        CountryInteraction::create([
            'user_id' => $event->user?->id,
            'country_id' => $event->country->Code,
            'interaction_type' => $event->interactionType,
            'session_id' => $event->sessionId ?? session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
