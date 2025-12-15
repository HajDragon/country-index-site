<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryLanguage extends Model
{
    protected $table = 'countrylanguage';

    protected $primaryKey = ['CountryCode', 'Language'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'CountryCode',
        'Language',
        'IsOfficial',
        'Percentage',

    ];

    public function country()
    {
        return $this->belongsTo(country::class, 'CountryCode', 'Code');
    }
}
