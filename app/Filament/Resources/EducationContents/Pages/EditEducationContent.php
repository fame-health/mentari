<?php

namespace App\Filament\Resources\EducationContents\Pages;

use App\Filament\Resources\EducationContents\EducationContentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEducationContent extends EditRecord
{
    protected static string $resource = EducationContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
