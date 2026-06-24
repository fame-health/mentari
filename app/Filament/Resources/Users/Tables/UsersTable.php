<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                TextColumn::make('name')
                    ->label('Pengguna')
                    ->description(fn (User $record): string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->wrap(),
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->description(fn (User $record): ?string => filled($record->level) ? 'Kelas '.$record->level : null)
                    ->placeholder('—')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'student' => 'Siswa',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => $state === 'admin' ? 'primary' : 'info'),
                TextColumn::make('can_take_screening')
                    ->label('Akses Screening')
                    ->badge()
                    ->formatStateUsing(fn (bool $state, User $record): string => match (true) {
                        $record->role !== 'student' => 'Tidak berlaku',
                        $state => 'Boleh Tes',
                        default => 'Sudah Tes',
                    })
                    ->color(fn (bool $state, User $record): string => match (true) {
                        $record->role !== 'student' => 'gray',
                        $state => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (bool $state, User $record): string => match (true) {
                        $record->role !== 'student' => 'heroicon-o-minus-circle',
                        $state => 'heroicon-o-check-circle',
                        default => 'heroicon-o-lock-closed',
                    }),
                TextColumn::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('streak_days')
                    ->label('Hari Beruntun')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('resetScreening')
                    ->label('Kasih akses screening')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Kasih akses screening lagi?')
                    ->modalDescription('Siswa dapat mengisi screening satu kali lagi. Riwayat hasil sebelumnya tetap tersimpan.')
                    ->visible(fn (User $record): bool => auth()->user()?->role === 'admin' && $record->role === 'student' && ! $record->can_take_screening)
                    ->action(function (User $record): void {
                        abort_unless(auth()->user()?->role === 'admin', 403);

                        $record->update(['can_take_screening' => true]);

                        Notification::make()
                            ->title('Akses screening berhasil diberikan')
                            ->body($record->name.' dapat mengikuti screening satu kali lagi.')
                            ->success()
                            ->send();
                    }),
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat detail'),
                    EditAction::make()
                        ->label('Ubah pengguna'),
                ])
                    ->label('Aksi pengguna')
                    ->tooltip('Aksi pengguna'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
