<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use App\Filament\Widgets\SchoolScreeningStats;
use App\Filament\Widgets\SchoolScreeningTrendChart;
use App\Filament\Widgets\SchoolSeverityDistributionChart;
use App\Models\School;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListSchoolScreeningResults extends ListRecords
{
    protected static string $resource = ScreeningResultResource::class;

    public School $school;

    public function mount(): void
    {
        $routeSchool = request()->route('school');
        $this->school = $routeSchool instanceof School
            ? $routeSchool
            : School::query()->findOrFail($routeSchool);

        parent::mount();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Hasil Screening — '.$this->school->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Data dan diagram hanya mencakup siswa yang terdaftar di sekolah ini.';
    }

    public function getBreadcrumb(): ?string
    {
        return $this->school->name;
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with('user:id,name,level,school_id')
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->school->id)
                ->where('role', 'student'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeSchool')
                ->label('Ganti sekolah')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ScreeningResultResource::getUrl('index')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SchoolScreeningStats::class,
            SchoolSeverityDistributionChart::class,
            SchoolScreeningTrendChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'schoolId' => $this->school->id,
        ];
    }
}
