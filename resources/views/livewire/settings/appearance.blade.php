<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $theme = 'system';

    public function mount(): void
    {
        $this->theme = auth()->user()->theme ?? 'system';
    }

    public function updatedTheme(): void
    {
        // Save theme preference to database
        auth()->user()->update(['theme' => $this->theme]);

        // Refresh the current user to get updated theme
        auth()->user()->refresh();

        // Redirect to current page to apply theme
        return redirect()->to(request()->url());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <flux:radio.group variant="segmented" wire:model.live="theme">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ __('Your preference has been saved automatically.') }}</p>
    </x-settings.layout>
</section>
