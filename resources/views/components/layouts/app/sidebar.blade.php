<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        {{-- Global loading indicator --}}
        <x-global-loading-indicator />

        {{ $slot }}

        @fluxScripts
    </body>
</html>
