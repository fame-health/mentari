<?php

namespace App\Filament\Resources\MoodEntries\Pages;

use App\Filament\Resources\MoodEntries\MoodEntryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMoodEntry extends ViewRecord
{
    protected static string $resource = MoodEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
