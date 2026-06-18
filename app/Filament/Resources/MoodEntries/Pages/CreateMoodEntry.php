<?php

namespace App\Filament\Resources\MoodEntries\Pages;

use App\Filament\Resources\MoodEntries\MoodEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMoodEntry extends CreateRecord
{
    protected static string $resource = MoodEntryResource::class;
}
