<?php

namespace App\Filament\Resources\ScreeningQuestions;

use App\Filament\Resources\ScreeningQuestions\Pages\CreateScreeningQuestion;
use App\Filament\Resources\ScreeningQuestions\Pages\EditScreeningQuestion;
use App\Filament\Resources\ScreeningQuestions\Pages\ListScreeningQuestions;
use App\Filament\Resources\ScreeningQuestions\Pages\ViewScreeningQuestion;
use App\Filament\Resources\ScreeningQuestions\Schemas\ScreeningQuestionForm;
use App\Filament\Resources\ScreeningQuestions\Schemas\ScreeningQuestionInfolist;
use App\Filament\Resources\ScreeningQuestions\Tables\ScreeningQuestionsTable;
use App\Models\ScreeningQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScreeningQuestionResource extends Resource
{
    protected static ?string $model = ScreeningQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Screening';

    protected static ?string $modelLabel = 'Pertanyaan Screening';

    protected static ?string $pluralModelLabel = 'Pertanyaan Screening';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ScreeningQuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScreeningQuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScreeningQuestionsTable::configure($table);
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
            'index' => ListScreeningQuestions::route('/'),
            'create' => CreateScreeningQuestion::route('/create'),
            'view' => ViewScreeningQuestion::route('/{record}'),
            'edit' => EditScreeningQuestion::route('/{record}/edit'),
        ];
    }
}
