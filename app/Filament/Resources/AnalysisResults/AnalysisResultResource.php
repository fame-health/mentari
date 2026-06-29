<?php

namespace App\Filament\Resources\AnalysisResults;

use App\Filament\Resources\AnalysisResults\Pages\ViewAnalysisResults;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class AnalysisResultResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'A (Analisis Data/Digital Dashboard)';

    protected static ?string $navigationLabel = 'Hasil Analisis Data';

    protected static ?string $modelLabel = 'Hasil Analisis Data';

    protected static ?string $pluralModelLabel = 'Hasil Analisis Data';

    protected static ?int $navigationSort = 3;

    protected static bool $isGloballySearchable = false;

    public static function getPages(): array
    {
        return [
            'index' => ViewAnalysisResults::route('/'),
        ];
    }
}
