<div class="relative">
    {{-- Scoped loading overlay - only for search, exports, and filters --}}
    <div
        wire:loading
        wire:target="performSearch,exportCsv,exportPdf,clearFilters,updatedSortBy,updatedSelectedContinents,updatedSelectedRegions,updatedPopulationMin,updatedPopulationMax,updatedLifeExpectancyMin,updatedLifeExpectancyMax,updatedShowFavoritesOnly"
        class="absolute inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60 backdrop-blur-sm rounded-lg"
    >
        <div class="flex flex-col items-center pt-100 gap-3">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-white border-t-blue-500"></div>
        <p class="text-white font-medium text-lg">Loading...</p>
        </div>
    </div>

    <x-mobile-navbar
        :continents="$continents"
        :regions="$regions"
        :population-min="$populationMin"
        :population-max="$populationMax"
        :life-expectancy-min="$lifeExpectancyMin"
        :life-expectancy-max="$lifeExpectancyMax"
        :show-filters="$showFilters"
    />

    {{-- Search and Sort --}}
    <div class="mb-6 hidden gap-4 md:flex md:flex-row">
        <div class="flex-1 gradient-border-input dark:gradient-border-input-dark rounded-full flex w-full">
            <flux:input
                wire:model="searchTerm"
                wire:keydown.enter="performSearch"
                type="text"
                placeholder="Search countries by name, region, or capital..."
                class="w-full rounded-l-full bg-white dark:bg-gray-900 focus:!ring-0 focus:!outline-none overflow-hidden border-r-0"
            />
            <flux:button wire:click="performSearch" class="rounded-r-full rounded-l-none" icon="magnifying-glass">
                Search
            </flux:button>
        </div>
        <div class="w-64">
           <flux:select wire:model.live="sortBy" class="w-full">
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
                <option value="population_desc">Population (High to Low)</option>
                <option value="population_asc">Population (Low to High)</option>
                <option value="continent">Continent</option>
           </flux:select>
        </div>
        <flux:button wire:click="$toggle('showFilters')" variant="ghost" size="sm">
            <div wire:loading.remove>
                {{ $showFilters ? 'Hide Filters' : '🔧Show Filters' }}
            </div>
            <div wire:loading>Loading Filters...</div>
        </flux:button>
    </div>

    {{-- Advanced Filters --}}
    @if($showFilters)
    <div class="mb-6 hidden rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 dark:text-white md:block">
        <div class="mb-4 flex items-center justify-between">
            <flux:heading size="lg" level="3">Advanced Filters</flux:heading>
            <flux:button wire:click="clearFilters" variant="ghost" size="sm">Clear All</flux:button>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            {{-- Continents Filter --}}
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

            {{-- Regions Filter --}}
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

            {{-- Population Range --}}
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

            {{-- Life Expectancy Range --}}
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
        </div>

        {{-- Favorites Filter (only show if authenticated) --}}
        @auth
        <div class="mt-4 flex items-center gap-2">
            <label class="flex items-center gap-2">
                <flux:checkbox wire:model.live="showFavoritesOnly" />
                <span class="text-sm">Show only my favorites</span>
            </label>
        </div>
        @endauth
    </div>
    @endif
    <div class="mb-6 hidden items-center justify-between md:flex">
        <flux:brand class="!bg-white !text-black border-rounded-4xl"><img src="/storage/Logo/Logo.png" alt="CountryPedia Logo" class="h-8 w-auto">CountryPedia</flux:brand>
        <flux:heading size="xl" level="1">Hey {{ Auth::user()->name }}, you are sailing from {{ Auth::user()->origin }} Welcome to the Country indexing site</flux:heading>
        <div class="flex items-center gap-4">
            {{-- Profile Image and User Menu --}}
            <div class="flex items-center gap-3">
                @if(Auth::user()->profile_image)
                    <img src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile" class="h-10 w-10 rounded-full object-cover">
                @else
                    <img src="{{ asset('default-avatar.png') }}" alt="Default Image" class="h-10 w-10 rounded-full object-cover">
                @endif

                <flux:button href="/stats">Statistics</flux:button>
                <flux:dropdown position="bottom" align="end">
                    <flux:button>
                        {{ Auth::user()->name }}
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item icon="star" href="/favorites">My Favorites</flux:menu.item>
                        <flux:menu.item icon="chart-pie" href="/analytics">Analytics</flux:menu.item>
                        <flux:menu.item icon="cog" href="/settings">Settings</flux:menu.item>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>

            {{-- Dark Mode Toggle --}}
            <livewire:actions.dark-mode-toggle />

            {{-- Export Buttons --}}
            <flux:dropdown position="bottom" align="end">
                <flux:button variant="ghost" icon="arrow-down-tray" size="sm">
                    Export
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="document" wire:click="exportCsv">
                        Download CSV
                    </flux:menu.item>
                    <flux:menu.item icon="arrow-down-tray" wire:click="exportPdf">
                        Download PDF
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <div class="grid auto-rows-min gap-4 md:grid-cols-3 lg:grid-cols-4">
        @foreach($countries as $country)
            <div
                wire:key="country-{{ $country->Code }}"
                data-aos="fade-down-right"
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700"
            >
                <livewire:country-card :countryCode="$country->Code" :key="$country->Code" />
            </div>
        @endforeach
    </div>

    {{-- Debug info --}}
    <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
        Showing {{ count($countries) }} of {{ $totalCount }} countries | Page: {{ $page }}
    </div>

    {{-- Infinite scroll trigger --}}
    @if($hasMore)
        <div
            x-data="{
                loading: false,
                observe() {
                    console.log('Setting up intersection observer for infinite scroll');
                    let observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            console.log('Intersection detected:', entry.isIntersecting, 'Loading:', this.loading);
                            if (entry.isIntersecting && !this.loading) {
                                console.log('Triggering loadMore...');
                                this.loading = true;
                                this.$wire.loadMore().then(() => {
                                    console.log('loadMore completed');
                                    // Reset loading immediately for faster subsequent loads
                                    setTimeout(() => {
                                        this.loading = false;
                                        console.log('Ready for next load');
                                    }, 100);
                                });
                            }
                        });
                    }, {
                        rootMargin: '400px',
                        threshold: 0.01
                    });
                    observer.observe(this.$el);
                    console.log('Intersection observer attached');
                }
            }"
            x-init="observe()"
            style="display: flex; justify-content: center; align-items: center; width: 100%;"
            class="mt-8 py-8"
        >
            <div wire:loading wire:target="loadMore" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">

                <p class="text-gray-600 dark:text-gray-400 font-medium">Loading more countries...</p>
            </div>
            <div wire:loading.remove wire:target="loadMore" style="text-align: center; width: 100%;">
                <span class="text-gray-400 dark:text-gray-600 text-sm">Scroll to load more (Page: {{ $page }})</span>
            </div>
        </div>
    @else
        <div style="display: flex; justify-content: center; align-items: center; width: 100%;" class="mt-8 py-8 flex-col gap-3">
            <p class="text-gray-600 dark:text-gray-400 font-medium">No more countries to load</p>
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Showing all {{ $totalCount }} {{ Str::plural('country', $totalCount) }}
            </p>
        </div>
    @endif
</div>
</div>
