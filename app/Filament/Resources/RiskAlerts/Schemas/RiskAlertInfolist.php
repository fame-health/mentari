<?php

namespace App\Filament\Resources\RiskAlerts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RiskAlertInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('screeningResult.id')
                    ->label('Screening result')
                    ->placeholder('-'),
                TextEntry::make('level')
                    ->badge(),
                TextEntry::make('title'),
                TextEntry::make('message')
                    ->columnSpanFull(),
                TextEntry::make('recommendation')
                    ->columnSpanFull(),
                TextEntry::make('dismissed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
