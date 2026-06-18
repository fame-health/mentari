<?php

namespace App\Filament\Resources\ScreeningAnswers;

use App\Filament\Resources\ScreeningAnswers\Pages\CreateScreeningAnswer;
use App\Filament\Resources\ScreeningAnswers\Pages\EditScreeningAnswer;
use App\Filament\Resources\ScreeningAnswers\Pages\ListScreeningAnswers;
use App\Filament\Resources\ScreeningAnswers\Pages\ViewScreeningAnswer;
use App\Filament\Resources\ScreeningAnswers\Schemas\ScreeningAnswerForm;
use App\Filament\Resources\ScreeningAnswers\Schemas\ScreeningAnswerInfolist;
use App\Filament\Resources\ScreeningAnswers\Tables\ScreeningAnswersTable;
use App\Models\ScreeningAnswer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScreeningAnswerResource extends Resource
{
    protected static ?string $model = ScreeningAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Screening';

    protected static ?string $modelLabel = 'Jawaban Screening';

    protected static ?string $pluralModelLabel = 'Jawaban Screening';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ScreeningAnswerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScreeningAnswerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScreeningAnswersTable::configure($table);
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
            'index' => ListScreeningAnswers::route('/'),
            'create' => CreateScreeningAnswer::route('/create'),
            'view' => ViewScreeningAnswer::route('/{record}'),
            'edit' => EditScreeningAnswer::route('/{record}/edit'),
        ];
    }
}
