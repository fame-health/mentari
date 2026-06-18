<?php

namespace App\Filament\Resources\MoodEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MoodEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('mood_option_id')
                    ->relationship('moodOption', 'label')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('entry_date')
                    ->required(),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('energy')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10),
                TextInput::make('stress')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10),
            ]);
    }
}
