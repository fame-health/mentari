<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('role')
                    ->badge(),
                TextColumn::make('level')
                    ->searchable(),
                TextColumn::make('avatar_initial')
                    ->searchable(),
                TextColumn::make('streak_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('can_take_screening')
                    ->label('Akses Screening')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Boleh Tes' : 'Sudah Tes')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-lock-closed'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('resetScreening')
                    ->label('Izinkan tes ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset akses screening siswa?')
                    ->modalDescription('Siswa dapat mengisi screening satu kali lagi. Riwayat hasil sebelumnya tetap tersimpan.')
                    ->visible(fn (User $record): bool => auth()->user()?->role === 'admin' && $record->role === 'student' && ! $record->can_take_screening)
                    ->action(function (User $record): void {
                        abort_unless(auth()->user()?->role === 'admin', 403);

                        $record->update(['can_take_screening' => true]);

                        Notification::make()
                            ->title('Akses screening berhasil direset')
                            ->body($record->name.' dapat mengikuti screening satu kali lagi.')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
