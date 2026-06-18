<?php

namespace App\Filament\Resources\MoodEntries\Pages;

use App\Filament\Resources\MoodEntries\MoodEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMoodEntry extends EditRecord
{
    protected static string $resource = MoodEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
