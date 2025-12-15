<div class="relative">
    <a href="{{ route('country.view', $country->Code) }}" wire:navigate class="block h-full overflow-y-auto p-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
    @if($country)
        <img src="https://flagsapi.com/{{ $country->Code2 }}/flat/64.png" alt="{{ $country->Name }} flag">
        <div class="mb-2 font-semibold">{{ $country->Name }}</div>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">Population: {{ number_format($country->Population) }}</div>
        <div class="mb-4 text-sm text-gray-400">Capital: {{ $country->capitalCity?->Name }}</div>
        <div class="mb-4 text-sm text-gray-400">Continent: {{ $country->Continent }}</div>

        <div class="space-y-2">
            @foreach($country->cities as $city)
                <div class="border-b border-gray-200 pb-2 dark:border-gray-700">
                    <div class="font-medium">{{ $city->Name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($city->Population) }}</div>
                </div>
            @endforeach
        </div>
    @else
        <div>Country not found in database</div>
    @endif
    </a>

    @if($country)
    <div class="absolute right-2 top-2">
        <a
            href="{{ route('countries.compare', ['codes' => $country->Code]) }}"
            wire:navigate
            class="rounded bg-blue-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-blue-700"
            onclick="event.stopPropagation();"
        >
            Compare
        </a>
    </div>
    @endif
</div>
