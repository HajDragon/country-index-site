<div class="h-full overflow-y-auto p-4">
    @if($China)
        <img src="https://flagsapi.com/CN/flat/64.png">
        <div class="mb-2 font-semibold">{{ $China->Name }}</div>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">Population: {{ number_format($China->Population) }}</div>
        <div class="space-y-2">
            @foreach($China->cities as $city)
                <div class="border-b border-gray-200 pb-2 dark:border-gray-700">

                    <div class="font-medium">{{ $city->Name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400"> Population: {{ number_format($city->Population) }}</div>
                </div>
            @endforeach
        </div>
    @else
        <div>Russia not found in database</div>
    @endif
</div>
