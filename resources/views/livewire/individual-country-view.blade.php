<div class="mx-auto max-w-5xl">
    {{-- Country Header with Flag --}}
    <div class="mb-8 flex items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <img src="https://flagsapi.com/{{ $country->Code2 }}/flat/64.png" alt="{{ $country->Name }} flag" class="h-16 w-24 rounded shadow-md">
            <div>
                <flux:heading size="2xl">{{ $country->Name }}</flux:heading>
                <flux:subheading>{{ $country->LocalName ?? $country->Name }}</flux:subheading>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <livewire:actions.dark-mode-toggle />
        </div>
    </div>
     <flux:button variant="ghost" size="sm" icon="arrow-left" onclick="window.location='{{ route('home') }}'">
                Back
            </flux:button>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- General Information --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <flux:heading size="lg" class="mb-4">General Information</flux:heading>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Code:</span>
                    <span class="font-semibold">{{ $country->Code }} ({{ $country->Code2 }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Continent:</span>
                    <span class="font-semibold">{{ $country->Continent }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Region:</span>
                    <span class="font-semibold">{{ $country->Region }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Capital:</span>
                    <span class="font-semibold">{{ $country->capitalCity?->Name ?? 'N/A' }}</span>
                </div>
                @if($country->IndepYear)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Independence Year:</span>
                    <span class="font-semibold">{{ $country->IndepYear }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Demographics & Economy --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <flux:heading size="lg" class="mb-4">Demographics & Economy</flux:heading>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Population:</span>
                    <span class="font-semibold">{{ number_format($country->Population) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Surface Area:</span>
                    <span class="font-semibold">{{ number_format($country->SurfaceArea, 2) }} km²</span>
                </div>
                @if($country->LifeExpectancy)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Life Expectancy:</span>
                    <span class="font-semibold">{{ $country->LifeExpectancy }} years</span>
                </div>
                @endif
                @if($country->GNP)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">GNP:</span>
                    <span class="font-semibold">${{ number_format($country->GNP) }} million</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Government --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <flux:heading size="lg" class="mb-4">Government</flux:heading>
            <div class="space-y-3 text-sm">
                @if($country->GovernmentForm)
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Form:</span>
                    <p class="mt-1 font-semibold">{{ $country->GovernmentForm }}</p>
                </div>
                @endif
                @if($country->HeadOfState)
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Head of State:</span>
                    <p class="mt-1 font-semibold">{{ $country->HeadOfState }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Languages --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <flux:heading size="lg" class="mb-4">Languages ({{ $country->languages->count() }})</flux:heading>
            <div class="max-h-48 space-y-2 overflow-y-auto text-sm">
                @forelse($country->languages->sortByDesc('Percentage') as $language)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                        <div>
                            <span class="font-semibold">{{ $language->Language }}</span>
                            @if($language->IsOfficial === 'T')
                                <flux:badge size="sm" color="green" class="ml-2">Official</flux:badge>
                            @endif
                        </div>
                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($language->Percentage, 1) }}%</span>
                    </div>
                @empty
                    <p class="text-gray-500">No language data available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Cities Section --}}
    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        <flux:heading size="lg" class="mb-4">Cities ({{ $cities->total() }})</flux:heading>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($cities as $city)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <div class="font-semibold">{{ $city->Name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $city->District }}</div>
                    <div class="mt-1 text-xs text-gray-500">Pop: {{ number_format($city->Population) }}</div>
                </div>
            @empty
                <p class="text-gray-500">No city data available</p>
            @endforelse
        </div>

        @if($cities->hasPages())
            <div class="mt-8 flex flex-col items-center gap-3">
                <nav class="flex items-center gap-1">
                    {{-- Previous Button --}}
                    @if ($cities->onFirstPage())
                        <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Previous</span>
                    @else
                        <a wire:click="previousPage" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">Previous</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($cities->getUrlRange(max(1, $cities->currentPage() - 1), min($cities->lastPage(), $cities->currentPage() + 1)) as $page => $url)
                        @if ($page == $cities->currentPage())
                            <span class="px-3 py-2 bg-blue-500 text-white rounded">{{ $page }}</span>
                        @else
                            <a wire:click="gotoPage({{ $page }})" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($cities->hasMorePages())
                        <a wire:click="nextPage" class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded">Next</a>
                    @else
                        <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Next</span>
                    @endif
                </nav>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $cities->firstItem() }} to {{ $cities->lastItem() }} of {{ $cities->total() }} cities
                </p>
            </div>
        @endif
    </div>
</div>
