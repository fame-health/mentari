<?php

namespace App\Filament\Resources\EducationCategories\Pages;

use App\Filament\Resources\EducationCategories\EducationCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEducationCategory extends EditRecord
{
    protected static string $resource = EducationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
