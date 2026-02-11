@props([
    'continents' => [],
    'regions' => [],
    'populationMin' => 0,
    'populationMax' => 0,
    'lifeExpectancyMin' => 0,
    'lifeExpectancyMax' => 0,
    'showFilters' => false,
])

<div class="mb-6 md:hidden" x-data="{ open: false }" data-test="mobile-navbar">
    <div class="flex items-center justify-start gap-3">
        <flux:button variant="ghost" size="sm" icon="bars-2" x-on:click="open = ! open">
            <span x-text="open ? 'Close' : 'Options'"></span>
        </flux:button>
    </div>

    <div x-cloak x-show="open" x-transition class="mt-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <div class="space-y-4">
            <div class="space-y-3">
                <flux:input
                    wire:model="searchTerm"
                    wire:keydown.enter="performSearch"
                    type="text"
                    placeholder="Search countries by name, region, or capital..."
                    class="w-full bg-white dark:bg-gray-900 focus:!ring-0 focus:!outline-none"
                />
                <flux:button wire:click="performSearch" class="w-full" icon="magnifying-glass">
                    Search
                </flux:button>
                <flux:select wire:model.live="sortBy" class="w-full">
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="population_desc">Population (High to Low)</option>
                    <option value="population_asc">Population (Low to High)</option>
                    <option value="continent">Continent</option>
                </flux:select>
                <flux:button wire:click="$toggle('showFilters')" variant="ghost" size="sm" class="w-full">
                    <div wire:loading.remove>
                        {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                    </div>
                    <div wire:loading>Loading Filters...</div>
                </flux:button>
            </div>

            @if($showFilters)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <div class="mb-3 flex items-center justify-between">
                        <flux:heading size="sm" level="3">Advanced Filters</flux:heading>
                        <flux:button wire:click="clearFilters" variant="ghost" size="sm">Clear All</flux:button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Continents</label>
                            <div class="space-y-2">
                                @foreach($continents as $continent)
                                    <label class="flex items-center gap-2">
                                        <flux:checkbox wire:model.live="selectedContinents" value="{{ $continent }}" />
                                        <span class="text-sm">{{ $continent }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Regions</label>
                            <div class="max-h-40 overflow-y-auto space-y-2">
                                @foreach($regions as $region)
                                    <label class="flex items-center gap-2">
                                        <flux:checkbox wire:model.live="selectedRegions" value="{{ $region }}" />
                                        <span class="text-sm truncate">{{ $region }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Population Range</label>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs text-gray-600 dark:text-gray-400">Min: {{ number_format($populationMin) }}</label>
                                    <input type="range" wire:model.live="populationMin" min="0" max="1400000000" step="10000000" class="w-full" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600 dark:text-gray-400">Max: {{ number_format($populationMax) }}</label>
                                    <input type="range" wire:model.live="populationMax" min="0" max="2000000000" step="10000000" class="w-full" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Life Expectancy (years)</label>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs text-gray-600 dark:text-gray-400">Min: {{ $lifeExpectancyMin }}</label>
                                    <input type="range" wire:model.live="lifeExpectancyMin" min="0" max="100" step="1" class="w-full" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600 dark:text-gray-400">Max: {{ $lifeExpectancyMax }}</label>
                                    <input type="range" wire:model.live="lifeExpectancyMax" min="0" max="100" step="1" class="w-full" />
                                </div>
                            </div>
                        </div>

                        @auth
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2">
                                    <flux:checkbox wire:model.live="showFavoritesOnly" />
                                    <span class="text-sm">Show only my favorites</span>
                                </label>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif

            <flux:navlist class="w-full">
                <flux:navlist.group heading="Quick Actions" expandable>
                    <flux:navlist.item icon="chart-bar" href="/stats">Statistics</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Export" expandable>
                    <flux:navlist.item icon="document" wire:click="exportCsv">Download CSV</flux:navlist.item>
                    <flux:navlist.item icon="arrow-down-tray" wire:click="exportPdf">Download PDF</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Account" expandable>
                    <div class="flex items-center gap-2 px-3 py-2">
                        @if(auth()->user()?->profile_image)
                            <img src="{{ Storage::url(auth()->user()->profile_image) }}" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" alt="Default Image" class="h-8 w-8 rounded-full object-cover">
                        @endif
                        <div class="text-sm">
                            <div class="font-medium">{{ auth()->user()?->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()?->origin }}</div>
                        </div>
                    </div>
                    <flux:navlist.item icon="star" href="/favorites">My Favorites</flux:navlist.item>
                    <flux:navlist.item icon="chart-pie" href="/analytics">Analytics</flux:navlist.item>
                    <flux:navlist.item icon="cog" href="/settings">Settings</flux:navlist.item>

                    <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                        @csrf
                        <flux:button type="submit" variant="ghost" class="w-full" icon="arrow-right-start-on-rectangle">
                            Log Out
                        </flux:button>
                    </form>
                </flux:navlist.group>

                <flux:navlist.group heading="Appearance" expandable>
                    <div class="flex items-center justify-between px-3 py-2">
                        <span class="text-sm">Dark mode</span>
                        <livewire:actions.dark-mode-toggle />
                    </div>
                </flux:navlist.group>
            </flux:navlist>
        </div>
    </div>
</div>
