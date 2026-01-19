@section('seo')
    {!! seo()->for($country) !!}
@endsection

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
<flux:button variant="ghost" size="sm" icon="arrow-left"
    onclick="wire.event.stopPropagation();"
    wire:click="goHome">
    <div wire:loading.remove>Back</div>
    <div wire:loading>Going back...</div>

    </flux:button>

    {{-- Map Section --}}
    @if($country->latitude && $country->longitude)
        <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <flux:heading size="lg" class="mb-4">Location on Map</flux:heading>
            <div id="country-map-{{ $country->Code }}" class="h-96 w-full rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800" style="height: 384px;" wire:ignore></div>
            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                Coordinates: {{ number_format($country->latitude, 4) }}°N, {{ number_format($country->longitude, 4) }}°E
            </div>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        {{-- Weather Card --}}
        <livewire:weather-card
            country-code="{{ $country->Code }}"
            :latitude="$country->latitude"
            :longitude="$country->longitude"
            :country-name="$country->Name"
        />

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

        {{-- Neighboring Countries --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <flux:heading size="lg" class="mb-4">Neighboring Countries</flux:heading>
            <div class="max-h-48 space-y-2 overflow-y-auto">
                @php
                    $neighbors = $country->getNeighbors();
                    // Fetch all neighbor countries in one query for efficiency
                    $neighborCodes = array_column($neighbors, 'code');
                    $neighborCountries = \App\Models\Country::whereIn('Code', $neighborCodes)
                        ->get()
                        ->keyBy('Code');
                @endphp
                @forelse($neighbors as $neighbor)
                    @php
                        $neighborCountry = $neighborCountries->get($neighbor['code']);
                        $code2 = $neighborCountry?->Code2 ?? strtoupper(substr($neighbor['code'], 0, 2));
                    @endphp
                    <a href="{{ route('country.view', ['countryCode' => $neighbor['code']]) }}"
                       class="flex items-center gap-3 rounded border border-gray-200 p-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                       wire:navigate>
                        <img src="https://flagsapi.com/{{ $code2 }}/flat/24.png"
                             class="h-4 w-6 rounded"
                             alt="{{ $neighbor['name'] }} flag">
                        <span class="font-medium text-sm">{{ $neighbor['name'] }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">No bordering countries or island nation</p>
                @endforelse
            </div>
        </div>

        {{-- Exchange Rates --}}
        @if($this->exchangeRates)
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <flux:heading size="lg" class="mb-4">
                    Currency Exchange Rates
                    <flux:badge size="sm" color="blue" class="ml-2">{{ $country->currency_code }}</flux:badge>
                </flux:heading>
                <div class="space-y-3 text-sm">
                    @if($country->currency_name)
                        <div class="mb-3 text-gray-600 dark:text-gray-400">
                            {{ $country->currency_name }}
                        </div>
                    @endif
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex justify-between rounded bg-gray-50 p-3 dark:bg-gray-800/50">
                            <span class="text-gray-600 dark:text-gray-400">1 {{ $this->exchangeRates['base'] }} to USD:</span>
                            <span class="font-semibold">${{ number_format($this->exchangeRates['usd'], 4) }}</span>
                        </div>
                        <div class="flex justify-between rounded bg-gray-50 p-3 dark:bg-gray-800/50">
                            <span class="text-gray-600 dark:text-gray-400">1 {{ $this->exchangeRates['base'] }} to EUR:</span>
                            <span class="font-semibold">€{{ number_format($this->exchangeRates['eur'], 4) }}</span>
                        </div>
                        <div class="flex justify-between rounded bg-gray-50 p-3 dark:bg-gray-800/50">
                            <span class="text-gray-600 dark:text-gray-400">1 {{ $this->exchangeRates['base'] }} to GBP:</span>
                            <span class="font-semibold">£{{ number_format($this->exchangeRates['gbp'], 4) }}</span>
                        </div>
                        <div class="flex justify-between rounded bg-gray-50 p-3 dark:bg-gray-800/50">
                            <span class="text-gray-600 dark:text-gray-400">1 {{ $this->exchangeRates['base'] }} to JPY:</span>
                            <span class="font-semibold">¥{{ number_format($this->exchangeRates['jpy'], 2) }}</span>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        Last updated: {{ $this->exchangeRates['lastUpdate'] }}
                    </div>
                </div>
            </div>
        @endif

        {{-- COVID-19 Statistics --}}
        @if($this->covidStats)
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <flux:heading size="lg" class="mb-4">
                    COVID-19 Statistics
                    <flux:badge size="sm" color="orange" class="ml-2">Live Data</flux:badge>
                </flux:heading>
                <div class="space-y-3 text-sm">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded bg-blue-50 p-3 dark:bg-blue-900/20">
                            <div class="text-xs text-gray-600 dark:text-gray-400">Total Cases</div>
                            <div class="text-lg font-bold">{{ number_format($this->covidStats['cases']) }}</div>
                            @if($this->covidStats['todayCases'] > 0)
                                <div class="text-xs text-blue-600 dark:text-blue-400">+{{ number_format($this->covidStats['todayCases']) }} today</div>
                            @endif
                        </div>
                        <div class="rounded bg-red-50 p-3 dark:bg-red-900/20">
                            <div class="text-xs text-gray-600 dark:text-gray-400">Total Deaths</div>
                            <div class="text-lg font-bold">{{ number_format($this->covidStats['deaths']) }}</div>
                            @if($this->covidStats['todayDeaths'] > 0)
                                <div class="text-xs text-red-600 dark:text-red-400">+{{ number_format($this->covidStats['todayDeaths']) }} today</div>
                            @endif
                        </div>
                        <div class="rounded bg-green-50 p-3 dark:bg-green-900/20">
                            <div class="text-xs text-gray-600 dark:text-gray-400">Recovered</div>
                            <div class="text-lg font-bold">{{ number_format($this->covidStats['recovered']) }}</div>
                        </div>
                        <div class="rounded bg-yellow-50 p-3 dark:bg-yellow-900/20">
                            <div class="text-xs text-gray-600 dark:text-gray-400">Active Cases</div>
                            <div class="text-lg font-bold">{{ number_format($this->covidStats['active']) }}</div>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 pt-3 dark:border-gray-700">
                        <div class="grid gap-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Cases per million:</span>
                                <span class="font-semibold">{{ number_format($this->covidStats['casesPerMillion']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Tests conducted:</span>
                                <span class="font-semibold">{{ number_format($this->covidStats['tests']) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        Last updated: {{ \Carbon\Carbon::parse($this->covidStats['updated'])->diffForHumans() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Cities Section --}}
    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        <div class="mb-4 flex items-center justify-between">
            <flux:heading size="lg">Cities ({{ $cities->total() }})</flux:heading>
            <x-loading-spinner target="nextPage,previousPage,gotoPage" text="Loading..." class="ml-2" />
        </div>

        <div wire:loading.remove wire:target="nextPage,previousPage,gotoPage" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
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

        {{-- Loading skeleton for cities --}}
        <div wire:loading wire:target="nextPage,previousPage,gotoPage" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @for ($i = 0; $i < 6; $i++)
                <div class="animate-pulse rounded-lg border border-gray-200 bg-gray-100 p-3 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-2 h-4 w-24 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    <div class="mb-1 h-3 w-32 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    <div class="h-2 w-16 bg-gray-300 dark:bg-gray-600 rounded"></div>
                </div>
            @endfor
        </div>

        @if($cities->hasPages())
            <div class="mt-8 flex flex-col items-center gap-3">
                <nav class="flex items-center gap-1">
                    {{-- Previous Button --}}
                    @if ($cities->onFirstPage())
                        <span class="px-3 py-2 text-gray-400 dark:text-gray-600">Previous</span>
                    @else
                        <a
                            wire:click="previousPage"
                            wire:loading.attr="disabled"
                            class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >Previous</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($cities->getUrlRange(max(1, $cities->currentPage() - 1), min($cities->lastPage(), $cities->currentPage() + 1)) as $page => $url)
                        @if ($page == $cities->currentPage())
                            <span class="px-3 py-2 bg-blue-500 text-white rounded">{{ $page }}</span>
                        @else
                            <a
                                wire:click="gotoPage({{ $page }})"
                                wire:loading.attr="disabled"
                                class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($cities->hasMorePages())
                        <a
                            wire:click="nextPage"
                            wire:loading.attr="disabled"
                            class="cursor-pointer px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 rounded disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >Next</a>
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

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
<script>
    @if($country->latitude && $country->longitude)
    document.addEventListener('livewire:navigated', function () {
        initCountryMap();
    });

    window.addEventListener('load', function() {
        initCountryMap();
    });

    function initCountryMap() {
        const mapId = 'country-map-{{ $country->Code }}';
        const mapElement = document.getElementById(mapId);
        
        if (!mapElement || mapElement.classList.contains('leaflet-container')) {
            return;
        }

        setTimeout(function() {
            try {
                const map = L.map(mapId).setView([{{ $country->latitude }}, {{ $country->longitude }}], 5);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 18
                }).addTo(map);
                
                const marker = L.marker([{{ $country->latitude }}, {{ $country->longitude }}]).addTo(map);
                marker.bindPopup(
                    '<div class="text-center p-2">' +
                    '<img src="https://flagsapi.com/{{ $country->Code2 }}/flat/32.png" alt="{{ $country->Name }} flag" class="mx-auto mb-2" style="height:20px; width:32px;">' +
                    '<strong>{{ $country->Name }}</strong><br>' +
                    @if($country->capitalCity)
                    'Capital: {{ $country->capitalCity->Name }}' +
                    @endif
                    '</div>'
                ).openPopup();
                
                setTimeout(function() {
                    map.invalidateSize();
                }, 200);
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        }, 300);
    }
    @endif
</script>
@endpush
