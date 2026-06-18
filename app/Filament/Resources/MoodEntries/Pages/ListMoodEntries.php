<?php

namespace App\Filament\Resources\MoodEntries\Pages;

use App\Filament\Resources\MoodEntries\MoodEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMoodEntries extends ListRecords
{
    protected static string $resource = MoodEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
