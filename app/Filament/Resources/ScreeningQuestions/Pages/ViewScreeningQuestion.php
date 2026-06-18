<?php

namespace App\Filament\Resources\ScreeningQuestions\Pages;

use App\Filament\Resources\ScreeningQuestions\ScreeningQuestionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScreeningQuestion extends ViewRecord
{
    protected static string $resource = ScreeningQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
