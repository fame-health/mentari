<?php

namespace App\Filament\Resources\EducationCategories\Pages;

use App\Filament\Resources\EducationCategories\EducationCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEducationCategory extends ViewRecord
{
    protected static string $resource = EducationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
