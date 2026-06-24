<?php

namespace App\Filament\Resources\EducationCategories\Pages;

use App\Filament\Resources\EducationCategories\EducationCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEducationCategories extends ListRecords
{
    protected static string $resource = EducationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah kategori')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah kategori edukasi')
                ->modalDescription('Slug dan urutan kategori akan dibuat otomatis.')
                ->modalSubmitActionLabel('Simpan kategori')
                ->createAnother(false),
        ];
    }
}
