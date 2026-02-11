<div class="relative h-full flex flex-col dark:text-white">
    <a href="{{ route('country.view', $country->Code) }}" wire:navigate class="flex-1 block overflow-y-auto p-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
    @if($country)
        <div class="mb-2 flex items-center justify-between">
            <img src="https://flagsapi.com/{{ $country->Code2 }}/flat/64.png" alt="{{ $country->Name }} flag" class="h-12 w-18">
            @php
                $currentTime = $country->getCurrentTime();
            @endphp
            @if($currentTime)
                <span class="rounded-full mt-10 bg-indigo-100 px-2 py-0.5 text-[10px] font-medium text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                    🕐 {{ $currentTime }}
                </span>
            @endif
        </div>
        <div class="mb-2 font-semibold">{{ $country->Name }}</div>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">Population: {{ number_format($country->Population) }}</div>
        <div class="mb-4 text-sm text-gray-400">Capital: {{ $country->capitalCity?->Name }}</div>
        <div class="mb-4 text-sm text-gray-400">Continent: {{ $country->Continent }}</div>

        {{-- <div class="space-y-2">
            @foreach($country->cities as $city)
                <div class="border-b border-gray-200 pb-2 dark:border-gray-700">
                    <div class="font-medium">{{ $city->Name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($city->Population) }}</div>
                </div>
            @endforeach
        </div> --}}
        @if (!empty($weatherData) && isset($weatherData['icon']))
            <span class="text-4xl">{{ $weatherData['icon'] }}</span>
            <div>
                <div class="text-3xl font-bold">{{ round($weatherData['temperature']) }}°C</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $weatherData['description'] ?? 'Weather unavailable' }}
                    <span class="text-gray-400 dark:text-gray-500">•</span>
                    {{ (int) ($weatherData['humidity'] ?? 0) }}% humidity
                    <span class="text-gray-400 dark:text-gray-500">•</span>
                    {{ round((float) ($weatherData['windSpeed'] ?? 0)) }} km/h wind
                </div>
            </div>
        @else
            <div class="text-sm text-gray-500 dark:text-gray-400">Weather data unavailable</div>
        @endif
    @else
        <div>Country not found in database</div>
    @endif
    </a>

    @if($country)
    <div class="absolute right-2 top-2 flex gap-2">
        <button
            wire:click="toggleFavorite"
            wire:loading.attr="disabled"
            wire:target="toggleFavorite"
            class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold text-gray-800 shadow hover:bg-gray-300 disabled:opacity-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
            onclick="event.stopPropagation();"
            title="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}"
        >
        @if (!$isFavorite)<div wire:loading.remove wire:target="toggleFavorite">♡</div>@endif
       @if ($isFavorite) <div wire:loading wire:target="toggleFavorite">💔</div>@endif
       @if (!$isFavorite) <div wire:loading wire:target="toggleFavorite">💞</div>@endif
        @if ($isFavorite)
            <div wire:loading.remove wire:target="toggleFavorite">♥</div>
        @endif

        </button>
        <flux:button variant="outline" size="sm"
            wire:click="goCompare"
            class="rounded bg-blue-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-blue-700"
            onclick="event.stopPropagation();"
        >
            <div wire:loading.remove wire:target="goCompare">Compare</div>
            <div wire:loading wire:target="goCompare">Comparing...</div>
        </flux:button>
    </div>
    @endif

</div>
