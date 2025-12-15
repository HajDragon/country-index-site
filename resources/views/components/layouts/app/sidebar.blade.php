<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()?->theme === 'dark' ? 'dark' : '' }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">

        {{ $slot }}

        @fluxScripts
    </body>
</html>
