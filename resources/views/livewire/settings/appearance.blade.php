<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public string $theme = 'system';

    public function mount(): void
    {
        $this->theme = auth()->user()->theme ?? 'system';
    }

    public function updateTheme(string $theme): void
    {
        $this->theme = $theme;
        auth()->user()->update(['theme' => $theme]);
        $this->dispatch('theme-updated', theme: $theme);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <flux:radio.group variant="segmented" wire:model.live="theme" wire:change="updateTheme($event.detail)">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ __('Your preference has been saved automatically.') }}</p>
    </x-settings.layout>
</section>
