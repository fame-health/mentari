<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RiskAlerts\RiskAlertResource;
use App\Models\RiskAlert;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentRiskAlerts extends TableWidget
{
    protected static ?string $heading = 'Alert Risiko Terbaru';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => RiskAlert::query()
                ->with('user')
                ->whereNull('dismissed_at')
                ->latest())
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable(),
                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'attention' => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('title')
                    ->label('Alert')
                    ->limit(45),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->recordActions([
                Action::make('buka')
                    ->label('Buka')
                    ->url(fn (RiskAlert $record): string => RiskAlertResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
