<?php

namespace App\Filament\Resources\RiskAlerts\Pages;

use App\Filament\Resources\RiskAlerts\RiskAlertResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRiskAlert extends EditRecord
{
    protected static string $resource = RiskAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
