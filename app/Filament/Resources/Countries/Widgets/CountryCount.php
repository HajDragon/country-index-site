<?php

namespace App\Filament\Resources\Countries\Widgets;

use App\Models\City;
use App\Models\Country;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CountryCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Countries', $this->formatInt(Country::count()))
                ->description('Total countries in database')
                ->icon('heroicon-o-globe-alt'),

            Stat::make('Continents', $this->formatInt(Country::distinct('Continent')->count('Continent')))
                ->description('Total continents represented')
                ->icon('heroicon-o-map'),

            Stat::make('Regions', $this->formatInt(Country::distinct('Region')->count('Region')))
                ->description('Total regions represented')
                ->icon('heroicon-o-globe-americas'),

            Stat::make('Total Cities', $this->formatInt(City::count()))
                ->description('Total cities in database')
                ->icon('heroicon-o-building-office'),

            Stat::make('total world population', $this->formatInt(Country::sum('Population')))
                ->description('Total population of all countries')
                ->icon('heroicon-o-user-plus'),

            Stat::make('Avg. Population', $this->formatInt((int) round(Country::avg('Population'))))
                ->description('Average population of countries')
                ->icon('heroicon-o-users'),

            Stat::make('Avg. Life Expectancy', $this->formatFloat((float) Country::avg('LifeExpectancy'), 1))
                ->description('Average life expectancy of countries')
                ->icon('heroicon-o-heart'),

            Stat::make('Avg. Surface Area', $this->formatFloat((float) Country::avg('SurfaceArea'), 2))
                ->description('Average surface area of countries (sq km)')
                ->icon('heroicon-o-globe-europe-africa'),

            Stat::make('Avg. GNP', $this->formatFloat((float) Country::avg('GNP'), 2))
                ->description('Average GNP of countries')
                ->icon('heroicon-o-currency-dollar'),

        ];
    }

    protected function formatInt(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }

    protected function formatFloat(float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals, ',', '.');
    }
}
