<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScreeningResults extends ListRecords
{
    protected static string $resource = ScreeningResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
