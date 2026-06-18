<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
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
                    ->options(['student' => 'Student', 'counselor' => 'Counselor', 'admin' => 'Admin'])
                    ->default('student')
                    ->required(),
                TextInput::make('level')
                    ->default(null),
                TextInput::make('avatar_initial')
                    ->maxLength(1)
                    ->default(null),
                TextInput::make('streak_days')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
