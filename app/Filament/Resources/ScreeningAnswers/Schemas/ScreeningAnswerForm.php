<?php

namespace App\Filament\Resources\ScreeningAnswers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ScreeningAnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('screening_result_id')
                    ->relationship('result', 'id')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Hasil screening'),
                Select::make('screening_question_id')
                    ->relationship('question', 'text')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Pertanyaan'),
                TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(3),
            ]);
    }
}
