<?php

namespace App\Filament\Resources\EducationContents\Tables;

use App\Models\EducationContent;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Artikel')
                    ->description(fn (EducationContent $record): ?string => filled($record->summary) ? str($record->summary)->limit(90)->toString() : null)
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->wrap(),
                TextColumn::make('category.title')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'article' => 'Artikel',
                        'video' => 'Video',
                        'audio' => 'Audio',
                        default => ucfirst($state),
                    }),
                TextColumn::make('read_time_minutes')
                    ->label('Durasi')
                    ->suffix(' menit')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label('Publikasi')
                    ->dateTime()
                    ->sortable(),
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ])
                    ->label('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
