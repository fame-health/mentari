<?php

namespace App\Filament\Resources\MoodOptions\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MoodOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                TextInput::make('emoji')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('description')
                    ->default(null),
                ColorPicker::make('color')
                    ->required(),
                TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
