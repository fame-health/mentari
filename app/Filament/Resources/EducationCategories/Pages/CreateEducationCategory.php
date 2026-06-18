<?php

namespace App\Filament\Resources\EducationCategories\Pages;

use App\Filament\Resources\EducationCategories\EducationCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEducationCategory extends CreateRecord
{
    protected static string $resource = EducationCategoryResource::class;
}
