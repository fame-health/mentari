<?php

namespace App\Filament\Resources\ScreeningResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScreeningResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('taken_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('depression_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('depression_severity')
                    ->badge(),
                TextColumn::make('anxiety_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('anxiety_severity')
                    ->badge(),
                TextColumn::make('stress_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stress_severity')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
