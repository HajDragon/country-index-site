<?php

namespace App\Exports;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CountriesExport implements FromQuery, WithHeadings, WithMapping
{
    protected ?Builder $query = null;

    public function __construct(?Builder $query = null)
    {
        $this->query = $query;
    }

    public function query(): Builder
    {
        return $this->query ?? Country::query();
    }

    public function headings(): array
    {
        return [
            'Country',
            'Code',
            'Continent',
            'Region',
            'Population',
            'Capital City',
            'Life Expectancy',
            'Surface Area (km²)',
        ];
    }

    public function map($country): array
    {
        return [
            $country->Name,
            $country->Code,
            $country->Continent,
            $country->Region,
            number_format($country->Population),
            $country->capitalCity?->Name ?? 'N/A',
            $country->LifeExpectancy ?? 'N/A',
            number_format($country->SurfaceArea),
        ];
    }
}
