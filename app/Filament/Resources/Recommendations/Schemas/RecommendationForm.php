<?php

namespace App\Filament\Resources\Recommendations\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('duration_minutes')
                    ->numeric()
                    ->default(null),
                TextInput::make('duration_label')
                    ->default(null),
                TextInput::make('priority')
                    ->default(null),
                ColorPicker::make('accent_color')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
