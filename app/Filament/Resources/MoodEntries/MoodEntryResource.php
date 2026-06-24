<?php

namespace App\Filament\Resources\MoodEntries;

use App\Filament\Resources\MoodEntries\Pages\CreateMoodEntry;
use App\Filament\Resources\MoodEntries\Pages\EditMoodEntry;
use App\Filament\Resources\MoodEntries\Pages\ListMoodEntries;
use App\Filament\Resources\MoodEntries\Pages\ViewMoodEntry;
use App\Filament\Resources\MoodEntries\Schemas\MoodEntryForm;
use App\Filament\Resources\MoodEntries\Schemas\MoodEntryInfolist;
use App\Filament\Resources\MoodEntries\Tables\MoodEntriesTable;
use App\Models\MoodEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MoodEntryResource extends Resource
{
    protected static ?string $model = MoodEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFaceSmile;

    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring Siswa';

    protected static ?string $modelLabel = 'Catatan Mood';

    protected static ?string $pluralModelLabel = 'Catatan Mood';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MoodEntryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MoodEntryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoodEntriesTable::configure($table);
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
            'index' => ListMoodEntries::route('/'),
            'create' => CreateMoodEntry::route('/create'),
            'view' => ViewMoodEntry::route('/{record}'),
            'edit' => EditMoodEntry::route('/{record}/edit'),
        ];
    }
}
