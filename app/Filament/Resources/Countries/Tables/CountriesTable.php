<?php

namespace App\Filament\Resources\Countries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Code')
                    ->searchable(),
                TextColumn::make('Name')
                    ->searchable(),
                TextColumn::make('Continent')
                    ->badge(),
                TextColumn::make('Region')
                    ->searchable(),
                TextColumn::make('SurfaceArea')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('IndepYear')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('Population')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('LifeExpectancy')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('GNP')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('GNPOld')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('LocalName')
                    ->searchable(),
                TextColumn::make('GovernmentForm')
                    ->searchable(),
                TextColumn::make('HeadOfState')
                    ->searchable(),
                TextColumn::make('Capital')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('Code2')
                    ->searchable(),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
