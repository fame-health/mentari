<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use App\Models\School;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListScreeningResults extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ScreeningResultResource::class;

    protected string $view = 'filament.resources.screening-results.pages.list-screening-result-schools';

    public function getTitle(): string|Htmlable
    {
        return 'Analisis Hasil Screening';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Pilih sekolah terlebih dahulu untuk melihat hasil screening dan diagram analisis siswanya.';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                School::query()
                    ->withCount([
                        'users as students_count' => fn (Builder $query): Builder => $query->where('role', 'student'),
                        'screeningResults as screening_results_count' => fn (Builder $query): Builder => $query->where('users.role', 'student'),
                        'riskAlerts as active_risk_alerts_count' => fn (Builder $query): Builder => $query
                            ->where('users.role', 'student')
                            ->whereNull('dismissed_at'),
                    ])
                    ->withMax([
                        'screeningResults as screening_results_max_taken_at' => fn (Builder $query): Builder => $query->where('users.role', 'student'),
                    ], 'taken_at'),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Sekolah')
                    ->description(fn (School $record): string => collect([$record->code, $record->address])->filter()->join(' · '))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('screening_results_count')
                    ->label('Total Screening')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('active_risk_alerts_count')
                    ->label('Alert Aktif')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('screening_results_max_taken_at')
                    ->label('Screening Terakhir')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum ada')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->recordUrl(fn (School $record): string => ScreeningResultResource::getUrl('school', ['school' => $record]))
            ->recordActions([
                Action::make('viewAnalysis')
                    ->label('Lihat hasil & analisis')
                    ->icon(Heroicon::OutlinedChartBarSquare)
                    ->url(fn (School $record): string => ScreeningResultResource::getUrl('school', ['school' => $record])),
            ])
            ->emptyStateIcon(Heroicon::OutlinedBuildingOffice2)
            ->emptyStateHeading('Belum ada sekolah')
            ->emptyStateDescription('Tambahkan sekolah terlebih dahulu sebelum melihat hasil screening.')
            ->paginated([10, 25, 50]);
    }
}
