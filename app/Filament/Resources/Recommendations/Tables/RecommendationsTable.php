<?php

namespace App\Filament\Resources\Recommendations\Tables;

use App\Models\Recommendation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecommendationsTable
{
    public static function configure(Table $table, bool $includeCategoryFilter = true): Table
    {
        $filters = [
            SelectFilter::make('severity')
                ->label('Status DASS-21')
                ->options(Recommendation::SEVERITY_LABELS),
        ];

        if ($includeCategoryFilter) {
            array_unshift(
                $filters,
                SelectFilter::make('category')
                    ->label('Jenis rekomendasi')
                    ->options(Recommendation::CATEGORY_LABELS),
            );
        }

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->description(fn (Recommendation $record): ?string => self::previewText($record))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Recommendation::CATEGORY_LABELS[$state] ?? ($state ?: '-'))
                    ->searchable(),
                TextColumn::make('severity')
                    ->label('Status DASS-21')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? Recommendation::SEVERITY_LABELS[$state] : '-')
                    ->color(fn (?string $state): string => match ($state) {
                        'normal' => 'success',
                        'mild' => 'info',
                        'moderate' => 'warning',
                        'severe', 'extremely_severe' => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters($filters)
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

    private static function previewText(Recommendation $record): ?string
    {
        $preview = $record->category === Recommendation::DASHBOARD_ANALYSIS_CATEGORY
            ? ($record->education_message ?: collect($record->main_points)->first())
            : $record->description;

        return filled($preview) ? str($preview)->limit(90)->toString() : null;
    }
}
