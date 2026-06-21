<?php

namespace App\Filament\Resources\EducationContents;

use App\Filament\Resources\EducationContents\Pages\CreateEducationContent;
use App\Filament\Resources\EducationContents\Pages\EditEducationContent;
use App\Filament\Resources\EducationContents\Pages\ListEducationContents;
use App\Filament\Resources\EducationContents\Pages\ViewEducationContent;
use App\Filament\Resources\EducationContents\Schemas\EducationContentForm;
use App\Filament\Resources\EducationContents\Schemas\EducationContentInfolist;
use App\Filament\Resources\EducationContents\Tables\EducationContentsTable;
use App\Models\EducationContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EducationContentResource extends Resource
{
    protected static ?string $model = EducationContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Konten Edukasi';

    protected static ?string $pluralModelLabel = 'Konten Edukasi';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return EducationContentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EducationContentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EducationContentsTable::configure($table);
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
            'index' => ListEducationContents::route('/'),
            'create' => CreateEducationContent::route('/create'),
            'view' => ViewEducationContent::route('/{record}'),
            'edit' => EditEducationContent::route('/{record}/edit'),
        ];
    }
}
