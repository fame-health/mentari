<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\Width;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Sekolah')
                    ->description(fn ($record): string => $record->code ?: 'Kode dibuat otomatis')
                    ->icon('heroicon-o-building-office-2')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(45)
                    ->placeholder('Belum diisi')
                    ->searchable(),
                TextColumn::make('students_count')
                    ->label('Siswa')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('screening_results_count')
                    ->label('Screening')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('active_risk_alerts_count')
                    ->label('Alert aktif')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading(fn ($record): string => 'Ringkasan '.$record->name)
                    ->modalDescription('Data sekolah dan jawaban otomatis dari sistem.')
                    ->modalWidth(Width::ThreeExtraLarge),
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading(fn ($record): string => 'Edit '.$record->name)
                    ->modalDescription('Kode sekolah tetap dikelola otomatis oleh sistem.')
                    ->modalSubmitActionLabel('Simpan perubahan')
                    ->modalWidth(Width::Large),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
