<?php

namespace App\Filament\Resources\EducationContents\Pages;

use App\Filament\Resources\EducationContents\EducationContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEducationContents extends ListRecords
{
    protected static string $resource = EducationContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
