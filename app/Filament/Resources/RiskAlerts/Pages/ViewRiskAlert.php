<?php

namespace App\Filament\Resources\RiskAlerts\Pages;

use App\Filament\Resources\RiskAlerts\RiskAlertResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRiskAlert extends ViewRecord
{
    protected static string $resource = RiskAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
