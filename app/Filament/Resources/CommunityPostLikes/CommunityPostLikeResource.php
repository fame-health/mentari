<?php

namespace App\Filament\Resources\CommunityPostLikes;

use App\Filament\Resources\CommunityPostLikes\Pages\CreateCommunityPostLike;
use App\Filament\Resources\CommunityPostLikes\Pages\EditCommunityPostLike;
use App\Filament\Resources\CommunityPostLikes\Pages\ListCommunityPostLikes;
use App\Filament\Resources\CommunityPostLikes\Pages\ViewCommunityPostLike;
use App\Filament\Resources\CommunityPostLikes\Schemas\CommunityPostLikeForm;
use App\Filament\Resources\CommunityPostLikes\Schemas\CommunityPostLikeInfolist;
use App\Filament\Resources\CommunityPostLikes\Tables\CommunityPostLikesTable;
use App\Models\CommunityPostLike;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommunityPostLikeResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = CommunityPostLike::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandThumbUp;

    protected static string|\UnitEnum|null $navigationGroup = 'Komunitas';

    protected static ?string $modelLabel = 'Like Postingan';

    protected static ?string $pluralModelLabel = 'Like Postingan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CommunityPostLikeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunityPostLikeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityPostLikesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommunityPostLikes::route('/'),
            'create' => CreateCommunityPostLike::route('/create'),
            'view' => ViewCommunityPostLike::route('/{record}'),
            'edit' => EditCommunityPostLike::route('/{record}/edit'),
        ];
    }
}
