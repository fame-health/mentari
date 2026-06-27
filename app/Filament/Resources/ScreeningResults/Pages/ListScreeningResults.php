<?php

namespace App\Filament\Resources\ScreeningResults\Pages;

use App\Filament\Resources\ScreeningResults\ScreeningResultResource;
use App\Models\Classroom;
use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class ListScreeningResults extends ListRecords
{
    protected static string $resource = ScreeningResultResource::class;

    protected string $view = 'filament.resources.screening-results.pages.list-screening-result-schools';

    private const ALL_CLASSROOMS = 'all';

    #[Url(as: 'school')]
    public ?int $selectedSchoolId = null;

    #[Url(as: 'class')]
    public ?string $selectedClassroomKey = null;

    protected Width|string|null $maxContentWidth = Width::Full;

    public function getTitle(): string|Htmlable
    {
        return 'Analisis Hasil Screening';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Pilih sekolah, lalu pilih semua kelas atau kelas tertentu untuk menampilkan data screening.';
    }

    public function getCurrentStep(): int
    {
        if (! $this->hasSelectedSchool()) {
            return 1;
        }

        return $this->hasSelectedClassroomOption() ? 3 : 2;
    }

    public function getSchools(): Collection
    {
        return School::query()
            ->withCount([
                'users as students_count' => fn (Builder $query): Builder => $query->where('role', 'student'),
                'screeningResults as screening_results_count' => fn (Builder $query): Builder => $query->where('users.role', 'student'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (School $school): array => [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code ?: 'Tanpa kode',
                'students_count' => $school->students_count,
                'screening_results_count' => $school->screening_results_count,
            ]);
    }

    public function getClassroomOptions(): Collection
    {
        if (! $this->hasSelectedSchool()) {
            return collect();
        }

        $screeningCounts = ScreeningResult::query()
            ->join('users', 'screening_results.user_id', '=', 'users.id')
            ->where('users.school_id', $this->selectedSchoolId)
            ->where('users.role', 'student')
            ->selectRaw('users.classroom_id, COUNT(*) as aggregate_count')
            ->groupBy('users.classroom_id')
            ->pluck('aggregate_count', 'users.classroom_id');

        $studentCount = User::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('role', 'student')
            ->count();

        $options = collect([[
            'value' => self::ALL_CLASSROOMS,
            'label' => 'Semua kelas',
            'description' => 'Gabungan seluruh kelas di sekolah ini',
            'students_count' => $studentCount,
            'screening_results_count' => $screeningCounts->sum(),
            'tone' => 'emerald',
            'icon' => 'heroicon-o-rectangle-stack',
        ]]);

        $classrooms = Classroom::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('is_active', true)
            ->withCount([
                'users as students_count' => fn (Builder $query): Builder => $query->where('role', 'student'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Classroom $classroom, int $index): array => [
                'value' => (string) $classroom->id,
                'label' => 'Kelas '.$classroom->name,
                'description' => 'Data khusus kelas '.$classroom->name,
                'students_count' => $classroom->students_count,
                'screening_results_count' => (int) ($screeningCounts[$classroom->id] ?? 0),
                'tone' => ['sky', 'indigo', 'amber', 'fuchsia'][$index % 4],
                'icon' => 'heroicon-o-academic-cap',
            ]);

        return $options->merge($classrooms)->values();
    }

    public function getSelectedSchoolLabel(): string
    {
        return $this->getSelectedSchool()?->name ?? 'Belum dipilih';
    }

    public function getSelectedClassroomLabel(): string
    {
        if ($this->selectedClassroomKey === self::ALL_CLASSROOMS) {
            return 'Semua kelas';
        }

        $classroomName = Classroom::query()
            ->whereKey($this->selectedClassroomKey)
            ->where('school_id', $this->selectedSchoolId)
            ->value('name');

        return $classroomName ? 'Kelas '.$classroomName : 'Kelas tidak ditemukan';
    }

    public function getAnalysisUrl(): string
    {
        return ScreeningResultResource::getUrl('school', [
            'school' => $this->getSelectedSchool(),
            'class' => $this->selectedClassroomKey,
        ]);
    }

    public function selectSchool(int $schoolId): void
    {
        abort_unless(School::query()->whereKey($schoolId)->exists(), 404);

        $this->selectedSchoolId = $schoolId;
        $this->selectedClassroomKey = null;
        $this->resetTable();
    }

    public function selectClassroom(string $classroomKey): void
    {
        abort_unless($this->getClassroomOptions()->pluck('value')->contains($classroomKey), 404);

        $this->selectedClassroomKey = $classroomKey;
        $this->resetTable();
    }

    public function backToSchools(): void
    {
        $this->selectedSchoolId = null;
        $this->selectedClassroomKey = null;
        $this->resetTable();
    }

    public function backToClassrooms(): void
    {
        $this->selectedClassroomKey = null;
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery()->with('user:id,name,level,school_id,classroom_id');

        if (! $this->hasSelectedClassroomOption()) {
            return $query->whereKey(-1);
        }

        return $query->whereHas('user', fn (Builder $query): Builder => $query
            ->where('school_id', $this->selectedSchoolId)
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
                ->url(fn (): string => $this->hasSelectedClassroomOption()
                    ? route('admin.screening-results.school.export.pdf', $this->exportRouteParameters())
                    : '#')
                ->visible(fn (): bool => $this->hasSelectedClassroomOption()),
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn (): string => $this->hasSelectedClassroomOption()
                    ? route('admin.screening-results.school.export.excel', $this->exportRouteParameters())
                    : '#')
                ->visible(fn (): bool => $this->hasSelectedClassroomOption()),
        ];
    }

    private function getSelectedSchool(): ?School
    {
        return $this->selectedSchoolId
            ? School::query()->find($this->selectedSchoolId)
            : null;
    }

    private function hasSelectedSchool(): bool
    {
        return $this->getSelectedSchool() !== null;
    }

    private function hasSelectedClassroomOption(): bool
    {
        if (! $this->hasSelectedSchool() || blank($this->selectedClassroomKey)) {
            return false;
        }

        return $this->getClassroomOptions()
            ->pluck('value')
            ->contains($this->selectedClassroomKey);
    }

    private function selectedClassroomId(): ?int
    {
        if ($this->selectedClassroomKey === self::ALL_CLASSROOMS) {
            return null;
        }

        return ctype_digit((string) $this->selectedClassroomKey)
            ? (int) $this->selectedClassroomKey
            : null;
    }

    private function exportRouteParameters(): array
    {
        return [
            'school' => $this->getSelectedSchool(),
            'class' => $this->selectedClassroomKey,
        ];
    }
}
