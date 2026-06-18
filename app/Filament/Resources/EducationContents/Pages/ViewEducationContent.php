<?php

namespace App\Filament\Resources\EducationContents\Pages;

use App\Filament\Resources\EducationContents\EducationContentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEducationContent extends ViewRecord
{
    protected static string $resource = EducationContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
