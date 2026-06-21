<?php

namespace App\Filament\Resources\RiskAlerts;

use App\Filament\Resources\RiskAlerts\Pages\CreateRiskAlert;
use App\Filament\Resources\RiskAlerts\Pages\EditRiskAlert;
use App\Filament\Resources\RiskAlerts\Pages\ListRiskAlerts;
use App\Filament\Resources\RiskAlerts\Pages\ViewRiskAlert;
use App\Filament\Resources\RiskAlerts\Schemas\RiskAlertForm;
use App\Filament\Resources\RiskAlerts\Schemas\RiskAlertInfolist;
use App\Filament\Resources\RiskAlerts\Tables\RiskAlertsTable;
use App\Models\RiskAlert;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RiskAlertResource extends Resource
{
    protected static ?string $model = RiskAlert::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|\UnitEnum|null $navigationGroup = 'Keselamatan';

    protected static ?string $modelLabel = 'Alert Risiko';

    protected static ?string $pluralModelLabel = 'Alert Risiko';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RiskAlertForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskAlertInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskAlertsTable::configure($table);
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
            'index' => ListRiskAlerts::route('/'),
            'create' => CreateRiskAlert::route('/create'),
            'view' => ViewRiskAlert::route('/{record}'),
            'edit' => EditRiskAlert::route('/{record}/edit'),
        ];
    }
}
