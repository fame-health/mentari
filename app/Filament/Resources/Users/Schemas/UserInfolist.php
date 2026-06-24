<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school.name')
                    ->label('School')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('role')
                    ->badge(),
                TextEntry::make('level')
                    ->placeholder('-'),
                TextEntry::make('avatar_initial')
                    ->placeholder('-'),
                TextEntry::make('streak_days')
                    ->numeric(),
                TextEntry::make('can_take_screening')
                    ->label('Akses Screening')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Boleh Tes' : 'Sudah Tes')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
