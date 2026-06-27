<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
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
                    ->extraAttributes(['class' => 'mentari-school-form-section mentari-school-form-section--identity'])
                    ->columns(2)
                    ->schema(self::identityFields()),
                Section::make('Daftar kelas')
                    ->description('Tambahkan kelas yang tersedia di sekolah ini. Kelas akan menjadi pilihan saat mengelola siswa.')
                    ->icon('heroicon-o-academic-cap')
                    ->extraAttributes(['class' => 'mentari-school-form-section mentari-school-form-section--classrooms'])
                    ->schema([
                        self::classroomsRepeater(),
                    ]),
            ]);
    }

    /**
     * @return array<Step>
     */
    public static function wizardSteps(): array
    {
        return [
            Step::make('Data Sekolah')
                ->description('Nama dan alamat')
                ->icon('heroicon-o-building-office-2')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('1. Isi Data Sekolah')
                        ->description('Mulai dari identitas utama sekolah. Kode sekolah akan dibuat otomatis saat disimpan.')
                        ->icon('heroicon-o-building-office-2')
                        ->extraAttributes(['class' => 'mentari-school-form-section mentari-school-form-section--identity'])
                        ->columns(2)
                        ->schema(self::identityFields()),
                ]),
            Step::make('Daftar Kelas')
                ->description('Tambahkan kelas')
                ->icon('heroicon-o-academic-cap')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('2. Isi Kelas Sekolah')
                        ->description('Tambahkan kelas yang tersedia. Data ini menjadi pilihan siswa saat admin mengelola pengguna.')
                        ->icon('heroicon-o-academic-cap')
                        ->extraAttributes(['class' => 'mentari-school-form-section mentari-school-form-section--classrooms'])
                        ->schema([
                            self::classroomsRepeater(),
                        ]),
                ]),
        ];
    }

    private static function identityFields(): array
    {
        return [
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
                ->autosize()
                ->maxLength(255)
                ->placeholder('Alamat lengkap sekolah (opsional)')
                ->columnSpanFull(),
        ];
    }

    private static function classroomsRepeater(): Repeater
    {
        return Repeater::make('classrooms')
            ->label('Kelas')
            ->relationship()
            ->schema([
                TextInput::make('name')
                    ->label('Nama kelas')
                    ->required()
                    ->maxLength(50)
                    ->distinct()
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
            ->columnSpanFull();
    }
}
