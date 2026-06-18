<?php

namespace App\Filament\Resources\EducationContents\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EducationContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('education_category_id')
                    ->relationship('category', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Kategori'),
                TextInput::make('title')
                    ->required(),
                Select::make('type')
                    ->options(['article' => 'Article', 'infographic' => 'Infographic', 'video' => 'Video'])
                    ->required(),
                TextInput::make('read_time_minutes')
                    ->numeric()
                    ->default(null),
                TextInput::make('read_time_label')
                    ->default(null),
                Textarea::make('summary')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('media_url')
                    ->url()
                    ->default(null),
                ColorPicker::make('accent_color')
                    ->default(null),
                DateTimePicker::make('published_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
