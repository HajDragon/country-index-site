<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'name_asc';

    public function mount(): void
    {
        if (!Auth::check()) {
            redirect()->route('login');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function removeFavorite($countryCode): void
    {
        Auth::user()->favoriteCountries()->where('country_code', $countryCode)->delete();
    }

    #[\Livewire\Attributes\Computed]
    public function countries()
    {
        if (!Auth::check()) {
            return collect();
        }

        $favoriteCountryCodes = Auth::user()->favoriteCountries()->pluck('country_code')->toArray();
        $query = Country::whereIn('Code', $favoriteCountryCodes);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('Name', 'like', "%{$this->search}%")
                    ->orWhere('Code', 'like', "%{$this->search}%")
                    ->orWhere('Region', 'like', "%{$this->search}%");
            });
        }

        $query = match ($this->sortBy) {
            'name_desc' => $query->orderBy('Name', 'desc'),
            'population_desc' => $query->orderBy('Population', 'desc'),
            'population_asc' => $query->orderBy('Population', 'asc'),
            default => $query->orderBy('Name', 'asc'),
        };

        return $query->with(['cities', 'capitalCity'])->paginate(12);
    }

    #[\Livewire\Attributes\Computed]
    public function favoriteCount()
    {
        return Auth::user()?->favoriteCountries()->count() ?? 0;
    }
}; ?>

<div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <flux:heading size="2xl" level="1">My Favorite Countries</flux:heading>
                <p class="mt-2 text-gray-600 dark:text-gray-400 ">
                    @if($this->favoriteCount > 0)
                        You have saved {{ $this->favoriteCount }} favorite {{ Str::plural('country', $this->favoriteCount) }}
                    @else
                        No favorites yet. Browse and click the heart icon to save your favorites!
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <livewire:actions.dark-mode-toggle />
                <a href="{{ route('home') }}" wire:navigate class="text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    ← Back to Countries
                </a>
            </div>
        </div>

        @if($this->favoriteCount > 0)
        {{-- Search and Sort --}}
        <div class="mb-6 flex gap-4">
            <div class="flex-1 gradient-border-input rounded-full ">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search favorites..."
                    class="w-full rounded-full bg-white dark:bg-gray-900 focus:!ring-0 focus:!outline-none overflow-hidden"
                />
            </div>
            <div class="w-64 ">
               <flux:select wire:model.live="sortBy" class="w-full">
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="population_desc">Population (High to Low)</option>
                    <option value="population_asc">Population (Low to High)</option>
               </flux:select>
            </div>
        </div>

        {{-- Favorites Grid --}}
        <div class="mb-8 grid auto-rows-min gap-4 md:grid-cols-3 lg:grid-cols-4 dark:text-white">
            @foreach($this->countries as $country)
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <a href="{{ route('country.view', $country->Code) }}" wire:navigate class="block h-full overflow-y-auto p-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <img src="https://flagsapi.com/{{ $country->Code2 }}/flat/64.png" alt="{{ $country->Name }} flag">
                        <div class="mb-2 font-semibold">{{ $country->Name }}</div>
                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">Population: {{ number_format($country->Population) }}</div>
                        <div class="mb-4 text-sm text-gray-400">Capital: {{ $country->capitalCity?->Name }}</div>
                        <div class="mb-4 text-sm text-gray-400">Continent: {{ $country->Continent }}</div>

                        <div class="space-y-2">
                            @foreach($country->cities->take(4) as $city)
                                <div class="border-b border-gray-200 pb-2 dark:border-gray-700">
                                    <div class="font-medium">{{ $city->Name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($city->Population) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </a>

                    <div class="absolute right-2 top-2 flex gap-2">
                        <button
                            wire:click="removeFavorite('{{ $country->Code }}')"
                            wire:loading.attr="disabled"
                            class="rounded bg-red-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-red-700 disabled:opacity-50"
                            title="Remove from favorites"
                        >
                            ❤️
                        </button>
                        <a
                            href="{{ route('countries.compare', ['codes' => $country->Code]) }}"
                            wire:navigate
                            class="rounded bg-blue-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-blue-700"
                        >
                            Compare
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex flex-col items-center gap-3">
            <nav class="flex items-center gap-1">
                {{-- Previous Button --}}
                @if ($this->countries->onFirstPage())
                    <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Previous</span>
                @else
                    <a wire:click="previousPage" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">Previous</a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($this->countries->getUrlRange(max(1, $this->countries->currentPage() - 1), min($this->countries->lastPage(), $this->countries->currentPage() + 1)) as $page => $url)
                    @if ($page == $this->countries->currentPage())
                        <span class="px-3 py-2 bg-blue-500 text-white rounded">{{ $page }}</span>
                    @else
                        <a wire:click="gotoPage({{ $page }})" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($this->countries->hasMorePages())
                    <a wire:click="nextPage" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">Next</a>
                @else
                    <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Next</span>
                @endif
            </nav>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $this->countries->firstItem() }} to {{ $this->countries->lastItem() }} of {{ $this->countries->total() }} results
            </p>
        </div>
        @else
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-12 text-center dark:border-gray-700 dark:bg-gray-800">
            <p class="text-gray-600 dark:text-gray-400">Start exploring countries and click the heart icon to add them to your favorites!</p>
            <a href="{{ route('home') }}" wire:navigate class="mt-4 inline-block text-blue-600 hover:text-blue-700 dark:text-blue-400">
                Browse Countries →
            </a>
        </div>
        @endif
    </div>
</div>
