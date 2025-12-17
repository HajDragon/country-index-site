<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
    <div class="mb-4">
        <flux:heading size="lg" class="mb-2">Current Weather in {{ $countryCapital ?: ($countryName ?: ($countryCode ?: 'this location')) }}</flux:heading>
        @if($timezone)
            @php
                try {
                    if (preg_match('/UTC([+-]\d{2}:\d{2})/', $timezone, $matches)) {
                        $offset = $matches[1];
                        $hours = (int) substr($offset, 0, 3);
                        $minutes = (int) substr($offset, 4, 2) * (substr($offset, 0, 1) === '-' ? -1 : 1);
                        $currentTime = \Illuminate\Support\Carbon::now('UTC')->addHours($hours)->addMinutes($minutes)->format('g:i A');
                    } else {
                        $currentTime = \Illuminate\Support\Carbon::now($timezone)->format('g:i A');
                    }
                } catch (\Exception $e) {
                    $currentTime = null;
                }
            @endphp
            @if($currentTime)
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <span class="rounded-full bg-indigo-100 px-2 py-0.5 font-medium text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                        🕐 {{ $currentTime }}
                    </span>
                    <span class="text-gray-500">{{ $timezone }}</span>
                </div>
            @endif
        @endif
    </div>

    @if($loading)`
        <div class="flex items-center justify-center py-8">
            <div class="text-center">
                <div class="mb-2 text-gray-400">
                    <svg class="mx-auto h-8 w-8 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Loading weather data...</p>
            </div>
        </div>
    @elseif($error)
        <div class="flex items-center justify-center py-8">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $error }}</p>
                <flux:button wire:click="fetchWeather" variant="ghost" size="sm" class="mt-2">
                    Retry
                </flux:button>
            </div>
        </div>
    @elseif($weatherData)
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-4xl">{{ $weatherData['icon'] }}</span>
                    <div>
                        <div class="text-3xl font-bold">{{ round($weatherData['temperature']) }}°C</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $weatherData['description'] }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 space-y-2 border-t border-gray-200 pt-4 text-sm dark:border-gray-700">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Humidity:</span>
                    <span class="font-semibold">{{ $weatherData['humidity'] }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Wind Speed:</span>
                    <span class="font-semibold">{{ round($weatherData['windSpeed'], 1) }} km/h</span>
                </div>
            </div>

            @if(!empty($weatherData['daily']))
                <div class="mt-5">
                    <flux:heading size="sm" class="mb-3 text-gray-700 dark:text-gray-300">Next 5 days</flux:heading>
                    <div class="grid grid-cols-5 gap-2 text-center text-sm">
                        @foreach($weatherData['daily'] as $day)
                            <div class="rounded-md border border-gray-200 p-3 dark:border-gray-700">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Carbon::parse($day['date'])->shortDayName }}
                                </div>
                                <div class="text-xl">{{ $day['icon'] }}</div>
                                <div class="mt-1 font-semibold">{{ round($day['tmax']) }}° / {{ round($day['tmin']) }}°</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="flex items-center justify-center py-8">
            <p class="text-sm text-gray-500 dark:text-gray-400">Weather data unavailable</p>
        </div>
    @endif
</div>
