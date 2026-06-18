<?php

namespace App\Filament\Resources\RiskAlerts\Pages;

use App\Filament\Resources\RiskAlerts\RiskAlertResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRiskAlerts extends ListRecords
{
    protected static string $resource = RiskAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
