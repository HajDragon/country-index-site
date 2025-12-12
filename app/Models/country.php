<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];


    public function cities(){
        return $this->hasMany(City::class, 'CountryCode', 'Code');
    }

    public function languages(){
        return $this->hasMany(CountryLanguage::class, 'CountryCode', 'Code');
    }

    public function capitalCity(){
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
}
