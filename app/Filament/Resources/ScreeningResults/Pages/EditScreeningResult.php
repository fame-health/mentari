<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditScreeningResult extends EditRecord
{
    protected static string $resource = ScreeningResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
