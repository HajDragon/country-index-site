<?php

declare(strict_types=1);

use App\Models\User;

it('shows the compare page when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/compare');

    $response->assertSuccessful();
    $response->assertSee('Compare Countries');
});
