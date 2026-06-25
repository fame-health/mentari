<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                Section::make('Daftar kelas')
                    ->description('Tambahkan kelas yang tersedia di sekolah ini. Kelas akan menjadi pilihan saat mengelola siswa.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Repeater::make('classrooms')
                            ->label('Kelas')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama kelas')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Contoh: X IPA 1'),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah kelas')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Kelas baru')
                            ->collapsible()
                            ->orderColumn('sort_order')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
