<?php

namespace App\Filament\Resources\ScreeningQuestions\Tables;

use App\Filament\Resources\ScreeningQuestions\ScreeningQuestionResource;
use App\Models\ScreeningQuestion;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ScreeningQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('No.')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('text')
                    ->label('Pertanyaan')
                    ->icon('heroicon-o-pencil-square')
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold)
                    ->wrap()
                    ->searchable()
                    ->grow()
                    ->action(
                        EditAction::make('editQuestion')
                            ->schema([
                                Textarea::make('text')
                                    ->label('Teks Pertanyaan')
                                    ->required()
                                    ->rows(5)
                                    ->maxLength(1000),
                            ])
                            ->modalHeading(fn (ScreeningQuestion $record): string => 'Edit Pertanyaan '.$record->number)
                            ->modalDescription('Perubahan teks langsung digunakan pada kuesioner DASS-21.')
                            ->modalSubmitActionLabel('Simpan pertanyaan')
                            ->successNotificationTitle('Pertanyaan berhasil diperbarui')
                            ->modalWidth(Width::Large),
                    ),
                TextColumn::make('scale')
                    ->label('Skala')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::scaleLabel($state))
                    ->color(fn (string $state): string => self::scaleColor($state))
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->alignCenter()
                    ->disabled(fn (ScreeningQuestion $record): bool => ! ScreeningQuestionResource::canEdit($record))
                    ->afterStateUpdated(function (bool $state): void {
                        Notification::make()
                            ->title($state ? 'Pertanyaan diaktifkan' : 'Pertanyaan dinonaktifkan')
                            ->success()
                            ->send();
                    }),
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
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('scale')
                    ->label('Skala')
                    ->options(self::scaleOptions()),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading(fn (ScreeningQuestion $record): string => 'Edit Pertanyaan '.$record->number)
                    ->modalDescription('Ubah seluruh data pertanyaan langsung dari tabel ini.')
                    ->modalSubmitActionLabel('Simpan perubahan')
                    ->successNotificationTitle('Pertanyaan berhasil diperbarui')
                    ->modalWidth(Width::Large),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus terpilih'),
                ]),
            ])
            ->paginated([10, 21, 50])
            ->defaultPaginationPageOption(21)
            ->striped()
            ->emptyStateIcon('heroicon-o-question-mark-circle')
            ->emptyStateHeading('Belum ada pertanyaan screening')
            ->emptyStateDescription('Tambahkan pertanyaan DASS-21 untuk mulai menyusun kuesioner.');
    }

    private static function scaleOptions(): array
    {
        return [
            'depression' => 'Depresi',
            'anxiety' => 'Kecemasan',
            'stress' => 'Stres',
        ];
    }

    private static function scaleLabel(string $scale): string
    {
        return self::scaleOptions()[$scale] ?? ucfirst($scale);
    }

    private static function scaleColor(string $scale): string
    {
        return match ($scale) {
            'depression' => 'danger',
            'anxiety' => 'warning',
            'stress' => 'info',
            default => 'gray',
        };
    }
}
