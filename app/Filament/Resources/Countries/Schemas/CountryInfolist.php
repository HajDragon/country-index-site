<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('Code'),
                TextEntry::make('Name'),
                TextEntry::make('Continent')
                    ->badge(),
                TextEntry::make('Region'),
                TextEntry::make('SurfaceArea')
                    ->numeric(),
                TextEntry::make('IndepYear')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('Population')
                    ->numeric(),
                TextEntry::make('LifeExpectancy')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('GNP')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('GNPOld')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('LocalName'),
                TextEntry::make('GovernmentForm'),
                TextEntry::make('HeadOfState')
                    ->placeholder('-'),
                TextEntry::make('Capital')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('Code2'),
                TextEntry::make('latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }
}
