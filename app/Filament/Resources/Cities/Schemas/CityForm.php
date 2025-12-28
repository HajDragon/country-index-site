<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Name')
                    ->required(),
                TextInput::make('CountryCode')
                    ->required(),
                TextInput::make('District')
                    ->required(),
                TextInput::make('Population')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
