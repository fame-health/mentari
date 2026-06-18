<?php

namespace App\Filament\Resources\CommunityPostLikes\Pages;

use App\Filament\Resources\CommunityPostLikes\CommunityPostLikeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommunityPostLikes extends ListRecords
{
    protected static string $resource = CommunityPostLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
