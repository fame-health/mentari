<?php

namespace App\Filament\Resources\RiskAlerts\Tables;

use App\Models\RiskAlert;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RiskAlertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->description(fn (RiskAlert $record): ?string => $record->user?->school?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('screeningResult.id')
                    ->label('ID Screening')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'urgent' => 'Urgent',
                        'attention' => 'Perlu Perhatian',
                        default => 'Stabil',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'attention' => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('title')
                    ->label('Alert')
                    ->searchable(),
                TextColumn::make('read_status')
                    ->label('Status')
                    ->state(fn (RiskAlert $record): string => $record->dismissed_at ? 'Telah Dibaca' : 'Belum Dibaca')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Telah Dibaca' ? 'success' : 'warning')
                    ->icon(fn (string $state): string => $state === 'Telah Dibaca' ? 'heroicon-o-check-circle' : 'heroicon-o-envelope'),
                TextColumn::make('dismissed_at')
                    ->label('Dibaca Pada')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('markAsRead')
                    ->label('Tandai telah dibaca')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai alert telah dibaca?')
                    ->visible(fn (RiskAlert $record): bool => auth()->user()?->role === 'admin' && $record->dismissed_at === null)
                    ->action(function (RiskAlert $record): void {
                        abort_unless(auth()->user()?->role === 'admin', 403);

                        $record->update(['dismissed_at' => now()]);

                        Notification::make()
                            ->title('Alert ditandai telah dibaca')
                            ->success()
                            ->send();
                    }),
                Action::make('markAsUnread')
                    ->label('Tandai belum dibaca')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Kembalikan status menjadi belum dibaca?')
                    ->visible(fn (RiskAlert $record): bool => auth()->user()?->role === 'admin' && $record->dismissed_at !== null)
                    ->action(function (RiskAlert $record): void {
                        abort_unless(auth()->user()?->role === 'admin', 403);

                        $record->update(['dismissed_at' => null]);

                        Notification::make()
                            ->title('Alert dikembalikan menjadi belum dibaca')
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
