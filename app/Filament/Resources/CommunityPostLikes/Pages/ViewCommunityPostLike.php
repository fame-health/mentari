<?php

namespace App\Filament\Resources\CommunityPostLikes\Pages;

use App\Filament\Resources\CommunityPostLikes\CommunityPostLikeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommunityPostLike extends ViewRecord
{
    protected static string $resource = CommunityPostLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
