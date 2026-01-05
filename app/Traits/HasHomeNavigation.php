<?php

namespace App\Traits;

trait HasHomeNavigation
{
    public function goHome(): void
    {
        $this->redirect(route('home'));
    }
}
