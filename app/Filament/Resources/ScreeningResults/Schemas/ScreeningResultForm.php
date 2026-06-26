<?php

namespace App\Filament\Resources\ScreeningResults\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ScreeningResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DateTimePicker::make('taken_at')
                    ->required(),
                TextInput::make('depression_score')
                    ->required()
                    ->numeric(),
                Select::make('depression_severity')
                    ->options([
                        'normal' => 'Normal',
                        'mild' => 'Mild',
                        'moderate' => 'Moderate',
                        'severe' => 'Severe',
                        'extremely_severe' => 'Extremely severe',
                    ])
                    ->required(),
                TextInput::make('anxiety_score')
                    ->required()
                    ->numeric(),
                Select::make('anxiety_severity')
                    ->options([
                        'normal' => 'Normal',
                        'mild' => 'Mild',
                        'moderate' => 'Moderate',
                        'severe' => 'Severe',
                        'extremely_severe' => 'Extremely severe',
                    ])
                    ->required(),
                TextInput::make('stress_score')
                    ->required()
                    ->numeric(),
                Select::make('stress_severity')
                    ->options([
                        'normal' => 'Normal',
                        'mild' => 'Mild',
                        'moderate' => 'Moderate',
                        'severe' => 'Severe',
                        'extremely_severe' => 'Extremely severe',
                    ])
                    ->required(),
                Textarea::make('summary')
                    ->required()
                    ->columnSpanFull(),
                Select::make('recommendation_id')
                    ->label('Rekomendasi personalisasi')
                    ->relationship('recommendation', 'title')
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih rekomendasi')
                    ->columnSpanFull(),
            ]);
    }
}
