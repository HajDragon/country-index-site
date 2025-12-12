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
    </div>

    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="xl" level="1">Hey {{ $this->getUserName() }}, Welcome to the Country indexing site</flux:heading>

        <flux:dropdown position="bottom" align="end">
            <flux:button variant="ghost" icon="user-circle" size="sm">
                {{ $this->getUserName() }}
            </flux:button>

            <flux:menu>

                <flux:menu.item icon="cog" href="/settings">Settings</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item icon="arrow-right-start-on-rectangle" href="/logout">Logout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
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
