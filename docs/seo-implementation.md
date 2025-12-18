# SEO Implementation Guide (ralphjsmit/laravel-seo)

This guide walks you through setting up and using `ralphjsmit/laravel-seo` in this Laravel 12 project, including Livewire v3 and Volt. Follow the steps and examples to add correct `<title>`, meta, OpenGraph, Twitter Card, canonical, robots, hreflang, and JSON‑LD tags.

## Prerequisites
- Laravel 12 (compatible with Laravel 10/11/12)
- PHP 8.0+
- This project’s stack: Livewire v3, Volt, Flux UI, Tailwind v4

## 1) Install the package
```bash
composer require ralphjsmit/laravel-seo
```
The service provider and facade are auto-discovered.

## 2) Publish config and migrations, then migrate
```bash
php artisan vendor:publish --tag="seo-config"
php artisan vendor:publish --tag="seo-migrations"
php artisan migrate
```
- Config: `config/seo.php`
- Migration creates the `seo` table used for the morph relation.

## 3) Configure sensible defaults
Edit `config/seo.php` (this project already sets good defaults):
- `site_name`: set to `config('app.name')`
- `title.suffix`: set to `" | ".config('app.name')` to append to every page title
- `favicon`: `"/favicon.ico"` (served from `public/`)
- `twitter.@username`: set via `SEO_TWITTER_USERNAME` in `.env` (without `@`)
- `canonical_link`: `true` ensures a self-referencing canonical link
- `robots.default`: defaults to modern search-friendly values

Example `.env` addition:
```dotenv
SEO_TWITTER_USERNAME=yourhandle
```

## 4) Render SEO tags in the `<head>`
This project renders tags in the global head partial:
- File: `resources/views/partials/head.blade.php`
- Pattern used:
```blade
@php($__seoStack = $__env->yieldPushContent('seo'))
{!! $__seoStack ?: seo() !!}
```
This means:
- If any page `@push('seo')` content exists, it renders that.
- Otherwise it falls back to `{!! seo() !!}` which renders defaults from `config/seo.php`.

Alternative (simpler): put `{!! seo() !!}` directly in your base layout head. Use the stack only if you want per‑page overrides.

## 5) Provide per-page SEO (Country page example)
For the country view, we push a page-specific set of tags:
- File: `resources/views/layouts/country-view.blade.php`
```blade
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
```
- `seo()->for($model)` tells the package to derive metadata from the associated model (see next section).
- The layout head picks up this `@push('seo')` output and renders it.

You can also push from a Livewire page view:
- File: `resources/views/livewire/individual-country-view.blade.php`
```blade
@section('seo')
    {!! seo()->for($country) !!}
@endsection
```

## 6) Dynamic SEO from Eloquent models
Add the trait and implement a method to describe page metadata.
- File: `app/Models/country.php`
```php
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Country extends Model
{
    use HasSEO; // Adds seo() morphOne relation and auto-creates a record

    public function getDynamicSEOData(): SEOData
    {
        $seo = new SEOData();

        $seo->title = $this->Name;
        $seo->openGraphTitle = $this->Name;
        $seo->description = trim(sprintf(
            '%s in %s, %s. Population %s. Capital %s.',
            $this->Name,
            $this->Region,
            $this->Continent,
            number_format((int) $this->Population),
            $this->capitalCity?->Name ?? 'N/A'
        ));

        $seo->url = route('country.view', ['countryCode' => $this->Code]);
        $seo->site_name = config('app.name');
        $seo->type = 'website';
        $seo->locale = app()->getLocale();
        $seo->enableTitleSuffix = true; // appends config title suffix

        return $seo;
    }
}
```
Notes:
- `HasSEO` auto-creates an `seo` record and provides `seo()` morphOne with defaults.
- `getDynamicSEOData()` returns `SEOData` the package will use at render time, merged with config defaults.

## 7) Livewire and Volt usage
- Keep the SEO helper in the base layout head so tags render server-side.
- For Livewire/Volt pages, either:
  - Push `seo()->for($model)` in the page’s Blade, or
  - Build a `SEOData` in the component and render via the layout using a section/stack override.
- Livewire lifecycle: set up the model in `mount()` and return the view in `render()`.

## 8) Advanced features you can use
- Canonical URL: derived automatically; override via `SEOData->canonical_url`.
- Robots: set per page via `SEOData->robots` (e.g., `noindex, nofollow`).
- Alternates (hreflang): provide `SEOData->alternates` to emit `<link rel="alternate" hreflang="...">`.
- OpenGraph: titles, description, image, site_name, type, publish/modify times for articles.
- Twitter Cards: set `imageMeta` to let the card choose large image vs summary.
- JSON‑LD: provide `SEOData->schema` with prepared schema objects or arrays. You can add Article, FAQPage, BreadcrumbList, etc.
- Global transformations: use `SEOManager::SEODataTransformer()` and `SEOManager::tagTransformer()` to modify all pages’ data/tags before render (e.g., enforce a default OG type).

## 9) Sitemaps
The package does not generate sitemap XML. Recommended companion:
```bash
composer require spatie/laravel-sitemap
```
- Generate a sitemap file (e.g., `public/sitemap.xml`) and set `config('seo.sitemap')` to the path (like `"/sitemap.xml"`). The package will link it in the head.

## 10) Testing your SEO
A minimal Pest test example (this project includes one):
- File: `tests/Feature/SeoCountryPageTest.php`
```php
use App\Models\Country;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders SEO tags for country page', function () {
    $user = User::factory()->create();

    // Avoid external search indexing during tests
    Country::withoutSyncingToSearch(function (): void {
        Country::query()->create([
            'Code' => 'XX', 'Name' => 'Testland', 'Continent' => 'Europe',
            'Region' => 'Test Region', 'Population' => 123456, 'SurfaceArea' => 1000.00,
            'Code2' => 'TL', 'latitude' => 0.0, 'longitude' => 0.0,
        ]);
    });

    actingAs($user);

    $response = get(route('country.view', ['countryCode' => 'XX']));
    $response->assertSuccessful();
    $response->assertSee('<title>Testland | '.config('app.name').'</title>', false);
    $response->assertSee('<meta name="description"', false);
});
```
Run it:
```bash
php artisan test tests/Feature/SeoCountryPageTest.php
```

## 11) Common pitfalls & troubleshooting
- Livewire pages: keep SEO rendering in the layout head; push overrides via Blade sections/stacks.
- Vite dev: if tags don’t appear (or head looks odd), try rebuilding assets:
  ```bash
  npm run dev
  # or
  npm run build
  ```
- Images: when you set `SEOData->image` to a relative path, the package will convert it to a secure URL and add image dimensions if available.
- Twitter username: set the env var without `@`; the package will add it.
- Tests + search: Laravel Scout may attempt to index models; use `withoutSyncingToSearch` in tests.

## 12) Reference
- GitHub: https://github.com/ralphjsmit/laravel-seo
- Packagist: https://packagist.org/packages/ralphjsmit/laravel-seo
- Laravel HTTP tests: https://laravel.com/docs/http-tests
- Spatie Sitemap: https://github.com/spatie/laravel-sitemap

With these steps, you can install, configure, and use SEO tags site-wide and per-page, including dynamic metadata from your Eloquent models. Adjust sections and defaults to your needs and test key routes to ensure tags render as expected.
