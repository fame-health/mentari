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
            CreateAction::make(),
        ];
    }
}
