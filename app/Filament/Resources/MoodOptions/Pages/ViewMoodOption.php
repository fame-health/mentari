<?php

namespace App\Filament\Resources\MoodOptions\Pages;

use App\Filament\Resources\MoodOptions\MoodOptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMoodOption extends ViewRecord
{
    protected static string $resource = MoodOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
