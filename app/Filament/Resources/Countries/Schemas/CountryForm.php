<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Name')
                    ->required(),
                Select::make('Continent')
                    ->options([
            'Asia' => 'Asia',
            'Europe' => 'Europe',
            'North America' => 'North america',
            'Africa' => 'Africa',
            'Oceania' => 'Oceania',
            'Antarctica' => 'Antarctica',
            'South America' => 'South america',
        ])
                    ->default('Asia')
                    ->required(),
                TextInput::make('Region')
                    ->required(),
                TextInput::make('SurfaceArea')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('IndepYear')
                    ->numeric(),
                TextInput::make('Population')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('LifeExpectancy')
                    ->numeric(),
                TextInput::make('GNP')
                    ->numeric(),
                TextInput::make('GNPOld')
                    ->numeric(),
                TextInput::make('LocalName')
                    ->required(),
                TextInput::make('GovernmentForm')
                    ->required(),
                TextInput::make('HeadOfState'),
                TextInput::make('Capital')
                    ->numeric(),
                TextInput::make('Code2')
                    ->required(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
            ]);
    }
}
