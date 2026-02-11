<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php($__seoStack = $__env->yieldPushContent('seo'))
{!! $__seoStack ?: seo() !!}

<script>
    // Prevent flash of wrong theme
    (function() {
        const darkMode = localStorage.getItem('darkMode') === 'true' ||
                        (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (darkMode) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

@stack('styles')
