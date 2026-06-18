<?php

namespace App\Filament\Resources\CommunityPostLikes\Pages;

use App\Filament\Resources\CommunityPostLikes\CommunityPostLikeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommunityPostLike extends EditRecord
{
    protected static string $resource = CommunityPostLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
