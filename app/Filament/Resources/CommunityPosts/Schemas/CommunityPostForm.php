<?php

namespace App\Filament\Resources\CommunityPosts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommunityPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->default(null),
                TextInput::make('tag')
                    ->default(null),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_pinned')
                    ->required(),
                TextInput::make('likes_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
