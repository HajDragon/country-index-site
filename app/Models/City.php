<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'city';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'CountryCode',
        'District',
        'Population',

    ];

    public function country(){
        return $this->belongsTo(Country::class, 'CountryCode', 'Code');
    }
}
