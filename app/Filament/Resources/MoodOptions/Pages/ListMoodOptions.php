<?php

namespace App\Filament\Resources\MoodOptions\Pages;

use App\Filament\Resources\MoodOptions\MoodOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMoodOptions extends ListRecords
{
    protected static string $resource = MoodOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
