<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Classroom;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('classroom_id', null);
                        $set('level', null);
                    })
                    ->default(null),
                Select::make('classroom_id')
                    ->label('Kelas')
                    ->options(fn (Get $get): array => Classroom::query()
                        ->where('school_id', $get('school_id'))
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->disabled(fn (Get $get): bool => blank($get('school_id')))
                    ->placeholder(fn (Get $get): string => filled($get('school_id'))
                        ? 'Pilih kelas'
                        : 'Pilih sekolah terlebih dahulu'),
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->minLength(8),
                Select::make('role')
                    ->label('Peran')
                    ->options(['student' => 'Siswa', 'counselor' => 'Konselor', 'admin' => 'Admin'])
                    ->default('student')
                    ->required(),
                TextInput::make('avatar_initial')
                    ->label('Inisial avatar')
                    ->maxLength(1)
                    ->default(null),
                TextInput::make('streak_days')
                    ->label('Hari beruntun')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
