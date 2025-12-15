<div>
    {{-- Search and Sort --}}
    <div class="mb-6 flex gap-4">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search countries by name, region, or capital..."
                class="w-full"
            />
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
            🔧 {{ $showFilters ? 'Hide' : 'Show' }} Filters
        </flux:button>
    </div>

    {{-- Advanced Filters --}}
    @if($showFilters)
    <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
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

    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="xl" level="1">Hey {{ Auth::user()->name }}, you are sailing from {{ Auth::user()->origin }} Welcome to the Country indexing site</flux:heading>

        <div class="flex gap-3">
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

            <flux:dropdown position="bottom" align="end">
                <flux:button variant="ghost" icon="user-circle" size="sm">
                    {{ Auth::user()->name }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="star" href="/favorites">My Favorites</flux:menu.item>
                    <flux:menu.item icon="chart-bar" href="/stats">Statistics</flux:menu.item>
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
    </div>

    <div class="grid auto-rows-min gap-4 md:grid-cols-3 lg:grid-cols-4">
        @foreach($countries as $country)
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <livewire:country-card :countryCode="$country->Code" :key="$country->Code" />
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex flex-col items-center gap-3">
        <nav class="flex items-center gap-1">
            {{-- Previous Button --}}
            @if ($countries->onFirstPage())
                <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Previous</span>
            @else
                <a wire:click="previousPage" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">Previous</a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($countries->getUrlRange(max(1, $countries->currentPage() - 1), min($countries->lastPage(), $countries->currentPage() + 1)) as $page => $url)
                @if ($page == $countries->currentPage())
                    <span class="px-3 py-2 bg-blue-500 text-white rounded">{{ $page }}</span>
                @else
                    <a wire:click="gotoPage({{ $page }})" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($countries->hasMorePages())
                <a wire:click="nextPage" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">Next</a>
            @else
                <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Next</span>
            @endif
        </nav>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing {{ $countries->firstItem() }} to {{ $countries->lastItem() }} of {{ $countries->total() }} results
        </p>
    </div>
</div>
