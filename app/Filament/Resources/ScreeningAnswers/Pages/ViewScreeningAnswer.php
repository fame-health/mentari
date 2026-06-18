<?php

namespace App\Filament\Resources\ScreeningAnswers\Pages;

use App\Filament\Resources\ScreeningAnswers\ScreeningAnswerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScreeningAnswer extends ViewRecord
{
    protected static string $resource = ScreeningAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
