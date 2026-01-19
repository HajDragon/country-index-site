<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory, HasSEO, Searchable;

    protected $table = 'country';

    protected $primaryKey = 'Code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'Code',
        'Name',
        'Continent',
        'Region',
        'SurfaceArea',
        'IndepYear',
        'Population',
        'LifeExpectancy',
        'GNP',
        'GNPOld',
        'LocalName',
        'GovernmentForm',
        'HeadOfState',
        'Capital',
        'Code2',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'SurfaceArea' => 'decimal:2',
        'Population' => 'integer',
        'LifeExpectancy' => 'decimal:1',
        'GNP' => 'decimal:2',
        'GNPOld' => 'decimal:2',
        'IndepYear' => 'integer',
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'CountryCode', 'Code');
    }

    public function languages()
    {
        return $this->hasMany(CountryLanguage::class, 'CountryCode', 'Code');
    }

    public function capitalCity()
    {
        return $this->belongsTo(City::class, 'Capital', 'ID');
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'Code' => $this->Code,
            'Name' => $this->Name,
            'Continent' => $this->Continent,
            'Region' => $this->Region,
            'Capital' => $this->capitalCity?->Name,
        ];
    }

    /**
     * Get the country's user favorites
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, 'country_code', 'Code');
    }

    /**
     * Get the country's interactions
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(CountryInteraction::class, 'country_id', 'Code');
    }

    /**
     * Get neighboring countries from REST Countries API
     *
     * @return array<int, array{code: string, name: string}>
     */
    public function getNeighbors(): array
    {
        $details = (new \App\Actions\FetchCountryDetails)->execute($this->Code);

        return $details['borders'] ?? [];
    }

    /**
     * Get timezones from REST Countries API
     *
     * @return array<int, string>
     */
    public function getTimezones(): array
    {
        $details = (new \App\Actions\FetchCountryDetails)->execute($this->Code);

        return $details['timezones'] ?? [];
    }

    /**
     * Get primary timezone (first timezone if multiple exist)
     */
    public function getPrimaryTimezone(): ?string
    {
        $timezones = $this->getTimezones();

        return ! empty($timezones) ? $timezones[0] : null;
    }

    /**
     * Provide dynamic SEO data for this country.
     */
    public function getDynamicSEOData(): SEOData
    {
        $seo = new SEOData;

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
        $seo->enableTitleSuffix = true;

        return $seo;
    }

    /**
     * Get current time in country's timezone formatted as 12-hour AM/PM
     */
    public function getCurrentTime(): ?string
    {
        $timezone = $this->getPrimaryTimezone();

        if (! $timezone) {
            return null;
        }

        try {
            // Parse UTC offset format (e.g., UTC+01:00, UTC-05:00)
            if (preg_match('/UTC([+-]\d{2}:\d{2})/', $timezone, $matches)) {
                $offset = $matches[1];

                return \Illuminate\Support\Carbon::now('UTC')
                    ->addHours((int) substr($offset, 0, 3))
                    ->addMinutes((int) substr($offset, 4, 2) * (substr($offset, 0, 1) === '-' ? -1 : 1))
                    ->format('g:i A');
            }

            // Try as IANA timezone
            return \Illuminate\Support\Carbon::now($timezone)->format('g:i A');
        } catch (\Exception) {
            return null;
        }
    }
}
