<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi sekolah')
                    ->description('Isi data yang perlu diketahui. Kode sekolah dibuat otomatis oleh sistem.')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama sekolah')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Contoh: SMA Negeri 1 Mentari')
                            ->autofocus()
                            ->columnSpanFull(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(255)
                            ->placeholder('Alamat lengkap sekolah (opsional)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
