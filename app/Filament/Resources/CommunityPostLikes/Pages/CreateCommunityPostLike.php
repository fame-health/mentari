<?php

namespace App\Filament\Resources\CommunityPostLikes\Pages;

use App\Filament\Resources\CommunityPostLikes\CommunityPostLikeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommunityPostLike extends CreateRecord
{
    protected static string $resource = CommunityPostLikeResource::class;
}
