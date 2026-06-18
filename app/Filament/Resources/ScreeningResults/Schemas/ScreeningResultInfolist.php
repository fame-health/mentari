<?php

namespace App\Filament\Resources\ScreeningResults\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScreeningResultInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('taken_at')
                    ->dateTime(),
                TextEntry::make('depression_score')
                    ->numeric(),
                TextEntry::make('depression_severity')
                    ->badge(),
                TextEntry::make('anxiety_score')
                    ->numeric(),
                TextEntry::make('anxiety_severity')
                    ->badge(),
                TextEntry::make('stress_score')
                    ->numeric(),
                TextEntry::make('stress_severity')
                    ->badge(),
                TextEntry::make('summary')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
