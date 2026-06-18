<?php

namespace App\Filament\Resources\RiskAlerts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiskAlertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('screening_result_id')
                    ->relationship('screeningResult', 'id')
                    ->default(null),
                Select::make('level')
                    ->options(['stable' => 'Stable', 'attention' => 'Attention', 'urgent' => 'Urgent'])
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('recommendation')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('dismissed_at'),
            ]);
    }
}
