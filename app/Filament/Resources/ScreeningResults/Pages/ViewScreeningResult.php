<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScreeningResult extends ViewRecord
{
    protected static string $resource = ScreeningResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
