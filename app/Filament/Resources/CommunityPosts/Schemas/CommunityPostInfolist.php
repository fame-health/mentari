<?php

namespace App\Filament\Resources\CommunityPosts\Schemas;

use App\Models\CommunityPost;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommunityPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('school.name')
                    ->label('School')
                    ->placeholder('-'),
                TextEntry::make('tag')
                    ->placeholder('-'),
                TextEntry::make('content')
                    ->columnSpanFull(),
                IconEntry::make('is_pinned')
                    ->boolean(),
                TextEntry::make('likes_count')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (CommunityPost $record): bool => $record->trashed()),
            ]);
    }
}
