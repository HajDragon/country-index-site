<?php

use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('dashboard');
})->name('home')
    ->middleware('auth');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('country', fn () => redirect()->route('home'));

    Route::get('country/{countryCode}', [CountryController::class, 'index'])->name('country.view');

    // Country comparison page
    Route::get('compare', function () {
        return view('layouts.compare');
    })->name('countries.compare');

    // Statistics and Favorites pages
    Volt::route('stats', 'pages.statistics')->name('stats');
    Volt::route('favorites', 'pages.favorites')->name('favorites');

    // Export routes
    Route::get('export/countries/csv', [CountryController::class, 'exportCsv'])->name('export.countries.csv');
    Route::get('export/countries/pdf', [CountryController::class, 'exportPdf'])->name('export.countries.pdf');
});
