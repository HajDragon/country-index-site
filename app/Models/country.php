<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory, Searchable;

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
}
