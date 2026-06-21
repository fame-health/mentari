<?php

namespace App\Filament\Resources\ScreeningResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScreeningResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->description(fn ($record): ?string => $record->user?->level ? 'Kelas '.$record->user->level : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('taken_at')
                    ->label('Waktu Screening')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('depression_score')
                    ->label('Skor Depresi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('depression_severity')
                    ->label('Depresi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::severityLabel($state))
                    ->color(fn (string $state): string => self::severityColor($state)),
                TextColumn::make('anxiety_score')
                    ->label('Skor Cemas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('anxiety_severity')
                    ->label('Kecemasan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::severityLabel($state))
                    ->color(fn (string $state): string => self::severityColor($state)),
                TextColumn::make('stress_score')
                    ->label('Skor Stres')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stress_severity')
                    ->label('Stres')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::severityLabel($state))
                    ->color(fn (string $state): string => self::severityColor($state)),
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
                SelectFilter::make('depression_severity')
                    ->label('Tingkat Depresi')
                    ->options(self::severityOptions()),
                SelectFilter::make('anxiety_severity')
                    ->label('Tingkat Kecemasan')
                    ->options(self::severityOptions()),
                SelectFilter::make('stress_severity')
                    ->label('Tingkat Stres')
                    ->options(self::severityOptions()),
            ])
            ->defaultSort('taken_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function severityOptions(): array
    {
        return [
            'normal' => 'Normal',
            'mild' => 'Ringan',
            'moderate' => 'Sedang',
            'severe' => 'Berat',
            'extremely_severe' => 'Sangat Berat',
        ];
    }

    private static function severityLabel(string $severity): string
    {
        return self::severityOptions()[$severity] ?? $severity;
    }

    private static function severityColor(string $severity): string
    {
        return match ($severity) {
            'normal' => 'success',
            'mild' => 'info',
            'moderate' => 'warning',
            'severe', 'extremely_severe' => 'danger',
            default => 'gray',
        };
    }
}
