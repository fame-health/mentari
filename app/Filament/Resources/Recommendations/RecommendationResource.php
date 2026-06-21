<?php

namespace App\Filament\Resources\Recommendations;

use App\Filament\Resources\Recommendations\Pages\CreateRecommendation;
use App\Filament\Resources\Recommendations\Pages\EditRecommendation;
use App\Filament\Resources\Recommendations\Pages\ListRecommendations;
use App\Filament\Resources\Recommendations\Pages\ViewRecommendation;
use App\Filament\Resources\Recommendations\Schemas\RecommendationForm;
use App\Filament\Resources\Recommendations\Schemas\RecommendationInfolist;
use App\Filament\Resources\Recommendations\Tables\RecommendationsTable;
use App\Models\Recommendation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RecommendationResource extends Resource
{
    protected static ?string $model = Recommendation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static string|\UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Rekomendasi';

    protected static ?string $pluralModelLabel = 'Rekomendasi';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return RecommendationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RecommendationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecommendationsTable::configure($table);
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
            'index' => ListRecommendations::route('/'),
            'create' => CreateRecommendation::route('/create'),
            'view' => ViewRecommendation::route('/{record}'),
            'edit' => EditRecommendation::route('/{record}/edit'),
        ];
    }
}
