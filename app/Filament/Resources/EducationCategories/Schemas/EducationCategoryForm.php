<?php

namespace App\Filament\Resources\EducationCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EducationCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama kategori')
                    ->required()
                    ->maxLength(120)
                    ->placeholder('Contoh: Kesehatan Mental'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->maxLength(255)
                    ->placeholder('Jelaskan isi kategori secara singkat')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Kategori aktif akan ditampilkan kepada pengguna.')
                    ->default(true)
                    ->required(),
            ]);
    }
}
