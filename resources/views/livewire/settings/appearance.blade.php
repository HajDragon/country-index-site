<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public string $theme = 'system';

    public function mount(): void
    {
        $this->theme = auth()->user()->theme ?? 'system';
    }

    #[On('updated-theme')]
    public function updateTheme(): void
    {
        auth()->user()->update(['theme' => $this->theme]);
        $this->dispatch('theme-updated', theme: $this->theme);
        // Also dispatch to window for Alpine to pick up
        $this->dispatch('theme-updated', ['theme' => $this->theme])->to('window');
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
