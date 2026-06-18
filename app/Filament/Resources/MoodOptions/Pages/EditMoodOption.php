<?php

namespace App\Filament\Resources\MoodOptions\Pages;

use App\Filament\Resources\MoodOptions\MoodOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMoodOption extends EditRecord
{
    protected static string $resource = MoodOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
