<?php

namespace App\Filament\Resources\EducationCategories;

use App\Filament\Resources\EducationCategories\Pages\EditEducationCategory;
use App\Filament\Resources\EducationCategories\Pages\ListEducationCategories;
use App\Filament\Resources\EducationCategories\Pages\ViewEducationCategory;
use App\Filament\Resources\EducationCategories\Schemas\EducationCategoryForm;
use App\Filament\Resources\EducationCategories\Schemas\EducationCategoryInfolist;
use App\Filament\Resources\EducationCategories\Tables\EducationCategoriesTable;
use App\Models\EducationCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EducationCategoryResource extends Resource
{
    protected static ?string $model = EducationCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'E (Edukasi Kesehatan Mental)';

    protected static ?string $modelLabel = 'Kategori Edukasi';

    protected static ?string $pluralModelLabel = 'Kategori Edukasi';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EducationCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EducationCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EducationCategoriesTable::configure($table);
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
            'index' => ListEducationCategories::route('/'),
            'view' => ViewEducationCategory::route('/{record}'),
            'edit' => EditEducationCategory::route('/{record}/edit'),
        ];
    }
}
