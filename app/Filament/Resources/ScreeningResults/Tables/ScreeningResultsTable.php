<?php

namespace App\Filament\Resources\ScreeningResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('taken_at')
                    ->label('Waktu Screening')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->taken_at?->diffForHumans()),

                // Depresi: skor + badge dalam satu kolom
                TextColumn::make('depression_score')
                    ->label('Depresi')
                    ->formatStateUsing(fn ($record): string => $record->depression_score.' poin')
                    ->description(fn ($record): string => self::severityLabel($record->depression_severity ?? ''))
                    ->badge()
                    ->color(fn ($record): string => self::severityColor($record->depression_severity ?? ''))
                    ->sortable(),

                // Kecemasan: skor + badge dalam satu kolom
                TextColumn::make('anxiety_score')
                    ->label('Kecemasan')
                    ->formatStateUsing(fn ($record): string => $record->anxiety_score.' poin')
                    ->description(fn ($record): string => self::severityLabel($record->anxiety_severity ?? ''))
                    ->badge()
                    ->color(fn ($record): string => self::severityColor($record->anxiety_severity ?? ''))
                    ->sortable(),

                // Stres: skor + badge dalam satu kolom
                TextColumn::make('stress_score')
                    ->label('Stres')
                    ->formatStateUsing(fn ($record): string => $record->stress_score.' poin')
                    ->description(fn ($record): string => self::severityLabel($record->stress_severity ?? ''))
                    ->badge()
                    ->color(fn ($record): string => self::severityColor($record->stress_severity ?? ''))
                    ->sortable(),

                // Kolom ringkasan risiko tertinggi
                TextColumn::make('risk_level')
                    ->label('Risiko Tertinggi')
                    ->getStateUsing(function ($record): string {
                        $worst = self::worstSeverity([
                            $record->depression_severity,
                            $record->anxiety_severity,
                            $record->stress_severity,
                        ]);

                        return self::severityLabel($worst);
                    })
                    ->badge()
                    ->color(function ($record): string {
                        $worst = self::worstSeverity([
                            $record->depression_severity,
                            $record->anxiety_severity,
                            $record->stress_severity,
                        ]);

                        return self::severityColor($worst);
                    })
                    ->sortable(false),

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
            ->striped()
            ->recordActions([
                ViewAction::make(),
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

    /**
     * Return the most severe (worst) severity level from the given list.
     */
    private static function worstSeverity(array $severities): string
    {
        $order = ['extremely_severe' => 5, 'severe' => 4, 'moderate' => 3, 'mild' => 2, 'normal' => 1];
        $worst = 'normal';
        $worstScore = 0;

        foreach ($severities as $s) {
            $score = $order[$s] ?? 0;
            if ($score > $worstScore) {
                $worstScore = $score;
                $worst = $s;
            }
        }

        return $worst;
    }
}
