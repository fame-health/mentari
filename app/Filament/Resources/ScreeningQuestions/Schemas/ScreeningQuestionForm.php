<?php

namespace App\Filament\Resources\ScreeningQuestions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ScreeningQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Nomor Pertanyaan')
                    ->required()
                    ->numeric(),
                Select::make('scale')
                    ->label('Skala')
                    ->options([
                        'depression' => 'Depresi',
                        'anxiety' => 'Kecemasan',
                        'stress' => 'Stres',
                    ])
                    ->required(),
                Textarea::make('text')
                    ->label('Teks Pertanyaan')
                    ->required()
                    ->rows(5)
                    ->maxLength(1000)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan Tampil')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
            ]);
    }
}
