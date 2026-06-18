<?php

namespace App\Filament\Resources\ScreeningResults;

use App\Filament\Resources\ScreeningResults\Pages\CreateScreeningResult;
use App\Filament\Resources\ScreeningResults\Pages\EditScreeningResult;
use App\Filament\Resources\ScreeningResults\Pages\ListScreeningResults;
use App\Filament\Resources\ScreeningResults\Pages\ViewScreeningResult;
use App\Filament\Resources\ScreeningResults\Schemas\ScreeningResultForm;
use App\Filament\Resources\ScreeningResults\Schemas\ScreeningResultInfolist;
use App\Filament\Resources\ScreeningResults\Tables\ScreeningResultsTable;
use App\Models\ScreeningResult;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScreeningResultResource extends Resource
{
    protected static ?string $model = ScreeningResult::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Screening';

    protected static ?string $modelLabel = 'Hasil Screening';

    protected static ?string $pluralModelLabel = 'Hasil Screening';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ScreeningResultForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScreeningResultInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScreeningResultsTable::configure($table);
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
            'index' => ListScreeningResults::route('/'),
            'create' => CreateScreeningResult::route('/create'),
            'view' => ViewScreeningResult::route('/{record}'),
            'edit' => EditScreeningResult::route('/{record}/edit'),
        ];
    }
}
