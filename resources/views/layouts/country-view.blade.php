@push('seo')
    @php
        $__countryForSeo = \App\Models\Country::with(['capitalCity', 'languages'])
            ->where('Code', $countryCode)
            ->first();
    @endphp
    @if($__countryForSeo)
        {!! seo()->for($__countryForSeo) !!}
    @endif
@endpush

<x-layouts.app :title="'Country Details'">
    <livewire:individual-country-view :countryCode="$countryCode" />
</x-layouts.app>
