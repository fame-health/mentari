<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use App\Filament\Widgets\SchoolScreeningStats;
use App\Filament\Widgets\SchoolScreeningTrendChart;
use App\Filament\Widgets\SchoolSeverityDistributionChart;
use App\Models\Classroom;
use App\Models\School;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListSchoolScreeningResults extends ListRecords
{
    protected static string $resource = ScreeningResultResource::class;

    private const ALL_CLASSROOMS = 'all';

    public School $school;

    #[Url(as: 'class')]
    public ?string $selectedClassroomKey = null;

    protected Width|string|null $maxContentWidth = Width::Full;

    public function mount(): void
    {
        $routeSchool = request()->route('school');
        $this->school = $routeSchool instanceof School
            ? $routeSchool
            : School::query()->findOrFail($routeSchool);

        if ($this->selectedClassroomKey && $this->selectedClassroomKey !== self::ALL_CLASSROOMS) {
            abort_unless($this->selectedClassroomId() !== null, 404);
        }

        parent::mount();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Hasil Screening - '.$this->school->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Data dan diagram mencakup '.$this->selectedClassroomLabel().' di sekolah ini.';
    }

    public function getBreadcrumb(): ?string
    {
        return $this->school->name;
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with('user:id,name,level,school_id,classroom_id')
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->school->id)
                ->when($this->selectedClassroomId(), fn (Builder $query, int $classroomId): Builder => $query->where('classroom_id', $classroomId))
                ->where('role', 'student'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(route('admin.screening-results.school.export.pdf', $this->exportRouteParameters())),
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(route('admin.screening-results.school.export.excel', $this->exportRouteParameters())),
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
            'md' => 6,
            'xl' => 12,
        ];
    }

    public function getPageClasses(): array
    {
        return [
            ...parent::getPageClasses(),
            'mentari-school-screening-results',
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'schoolId' => $this->school->id,
            'classroomId' => $this->selectedClassroomId(),
        ];
    }

    private function selectedClassroomId(): ?int
    {
        if (blank($this->selectedClassroomKey) || $this->selectedClassroomKey === self::ALL_CLASSROOMS) {
            return null;
        }

        if (! ctype_digit((string) $this->selectedClassroomKey)) {
            return null;
        }

        $classroomId = (int) $this->selectedClassroomKey;

        return Classroom::query()
            ->where('school_id', $this->school->id)
            ->whereKey($classroomId)
            ->exists()
            ? $classroomId
            : null;
    }

    private function selectedClassroomLabel(): string
    {
        $classroomId = $this->selectedClassroomId();

        if (! $classroomId) {
            return 'semua kelas';
        }

        $classroomName = Classroom::query()
            ->where('school_id', $this->school->id)
            ->whereKey($classroomId)
            ->value('name');

        return $classroomName ? 'kelas '.$classroomName : 'kelas terpilih';
    }

    private function exportRouteParameters(): array
    {
        $parameters = ['school' => $this->school];

        if (filled($this->selectedClassroomKey)) {
            $parameters['class'] = $this->selectedClassroomKey;
        }

        return $parameters;
    }
}
