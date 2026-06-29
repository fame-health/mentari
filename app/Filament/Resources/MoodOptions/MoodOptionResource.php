<?php

namespace App\Filament\Resources\MoodOptions;

use App\Filament\Resources\MoodOptions\Pages\CreateMoodOption;
use App\Filament\Resources\MoodOptions\Pages\EditMoodOption;
use App\Filament\Resources\MoodOptions\Pages\ListMoodOptions;
use App\Filament\Resources\MoodOptions\Pages\ViewMoodOption;
use App\Filament\Resources\MoodOptions\Schemas\MoodOptionForm;
use App\Filament\Resources\MoodOptions\Schemas\MoodOptionInfolist;
use App\Filament\Resources\MoodOptions\Tables\MoodOptionsTable;
use App\Models\MoodOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MoodOptionResource extends Resource
{
    protected static ?string $model = MoodOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|\UnitEnum|null $navigationGroup = 'M (Mood Tracking Harian)';

    protected static ?string $modelLabel = 'Pilihan Mood';

    protected static ?string $pluralModelLabel = 'Pilihan Mood';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MoodOptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MoodOptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoodOptionsTable::configure($table);
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
            'index' => ListMoodOptions::route('/'),
            'create' => CreateMoodOption::route('/create'),
            'view' => ViewMoodOption::route('/{record}'),
            'edit' => EditMoodOption::route('/{record}/edit'),
        ];
    }
}
