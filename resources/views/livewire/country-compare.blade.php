<div>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end">
        <div class="flex-1">
            <flux:button variant="ghost" size="sm" icon="arrow-left" onclick="window.location.href='{{ route('home') }}'">
                Back

            </flux:button>
            <flux:heading size="xl">Compare Countries</flux:heading>
            <flux:subheading>Select 2–3 countries to compare side-by-side.</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <livewire:actions.dark-mode-toggle />
            <div class="gradient-border-input min-w-250 rounded-full">
                <flux:input
                    wire:model.live.debounce.300ms="query"
                    placeholder="Search by name or code (e.g., USA)"
                    class="w-72 rounded-full bg-white dark:bg-gray-900 focus:!ring-0 focus:!outline-none overflow-hidden"
                />
            </div>
            <flux:button variant="ghost" wire:click="clear">Clear</flux:button>
            <div x-data="{copied:false}" class="inline-flex">
                <flux:button
                    variant="primary"
                    x-on:click="navigator.clipboard.writeText(window.location.href); copied=true; setTimeout(()=>copied=false, 1200)"
                >
                    <span x-show="!copied">Copy Link</span>
                    <span x-show="copied">Copied!</span>
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Suggestions --}}
    @if(strlen($query) >= 2 && $suggestions->isNotEmpty())
        <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                @foreach($suggestions as $s)
                    <button
                        wire:click="addCode('{{ $s->Code }}')"
                        class="flex items-center justify-between rounded border  border-gray-200 p-2 text-left hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-white"
                    >
                        <span>
                            <span class="font-semibold">{{ $s->Name }}</span>
                            <span class="ml-2 text-xs text-gray-500">{{ $s->Code }}</span>
                        </span>
                        <img src="https://flagsapi.com/{{ $s->Code2 }}/flat/24.png" class="h-5 w-7 rounded" alt="flag">
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Selected chips --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @forelse($selectedCodes as $code)
            <div class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <span class="font-semibold">{{ $code }}</span>
                <button wire:click="removeCode('{{ $code }}')" class="text-gray-500 hover:text-red-600">&times;</button>
            </div>
        @empty
            <flux:text>Add up to three countries to compare.</flux:text>
        @endforelse
    </div>

    {{-- Guidance when less than 2 selected --}}
    @if(count($selectedCodes) < 2)
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-400 ">
            Pick at least two countries to see a comparison. Use the search above to add countries.
        </div>
    @else
        {{-- Comparison grid --}}
        <div class="grid gap-4" style="grid-template-columns: repeat({{ max(2, min(3, count($countries))) }}, minmax(0, 1fr));">
            @foreach($countries as $country)
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900 dark:text-white" >
                    <div class="mb-3 flex items-center gap-3">
                        <img src="https://flagsapi.com/{{ $country->Code2 }}/flat/32.png" class="h-6 w-10 rounded" alt="flag">
                        <div>
                            <div class="font-semibold">{{ $country->Name }}</div>
                            <div class="text-xs text-gray-500">{{ $country->LocalName ?? $country->Name }}</div>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Code</span><span class="font-medium">{{ $country->Code }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Continent</span><span class="font-medium">{{ $country->Continent }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Region</span><span class="font-medium">{{ $country->Region }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Capital</span><span class="font-medium">{{ $country->capitalCity?->Name ?? 'N/A' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Population</span><span class="font-medium">{{ number_format($country->Population) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Area (km²)</span><span class="font-medium">{{ number_format($country->SurfaceArea, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Life Expectancy</span><span class="font-medium">{{ $country->LifeExpectancy ?? 'N/A' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">GNP</span><span class="font-medium">{{ $country->GNP ? '$'.number_format($country->GNP) : 'N/A' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Cities</span><span class="font-medium">{{ $country->cities_count }}</span></div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Timezone</span>
                            <span class="font-medium text-xs">{{ $country->getPrimaryTimezone() ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Neighbors</span>
                            <span class="font-medium">{{ count($country->getNeighbors()) }}</span>
                        </div>

                        {{-- weather card --}}
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                        <div class="mb-2 text-xs uppercase text-gray-500">Top Languages</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($country->languages->sortByDesc('Percentage')->take(3) as $lang)
                            <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-2 py-0.5 text-xs dark:border-gray-700">
                                {{ $lang->Language }}
                                @if($lang->IsOfficial === 'T')
                                <span class="text-[10px] text-green-600">• Official</span>
                                @endif
                                <span class="text-[10px] text-gray-500">{{ number_format($lang->Percentage, 1) }}%</span>
                            </span>
                            @endforeach
                            @if($country->languages->isEmpty())
                            <span class="text-xs text-gray-500">N/A</span>
                            @endif
                        </div>
                    </div>
                    <livewire:weather-card
                        wire:key="weather-card-{{ $country->Code }}"
                        country-code="{{ $country->Code }}"
                        :latitude="$country->latitude"
                        :longitude="$country->longitude"
                        :country-name="$country->Name" />
                </div>
            @endforeach
        </div>
    @endif
</div>
