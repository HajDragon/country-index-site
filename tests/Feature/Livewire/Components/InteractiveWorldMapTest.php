<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('components.interactive-world-map');

    $component->assertSee('');
});
