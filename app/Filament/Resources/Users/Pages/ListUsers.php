<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.users.pages.list-users-by-school-and-class';

    private const EMPTY_LEVEL = '__empty__';

    private const EMPTY_SCHOOL = 0;

    #[Url(as: 'school')]
    public ?int $selectedSchoolId = null;

    #[Url(as: 'class')]
    public ?string $selectedLevel = null;

    public function getTitle(): string|Htmlable
    {
        return 'Pengguna Berdasarkan Sekolah dan Kelas';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Pilih sekolah dan kelas terlebih dahulu untuk menampilkan daftar pengguna.';
    }

    public function getCurrentStep(): int
    {
        if (! $this->hasSelectedSchool()) {
            return 1;
        }

        return filled($this->selectedLevel) ? 3 : 2;
    }

    public function getSchools(): Collection
    {
        $schools = School::query()
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn (School $school): array => [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code,
                'users_count' => $school->users_count,
            ]);

        $usersWithoutSchool = User::query()->whereNull('school_id')->count();

        if ($usersWithoutSchool > 0) {
            $schools->push([
                'id' => self::EMPTY_SCHOOL,
                'name' => 'Sekolah belum diisi',
                'code' => 'Belum ditautkan',
                'users_count' => $usersWithoutSchool,
            ]);
        }

        return $schools;
    }

    public function getSelectedSchool(): ?School
    {
        if (! $this->selectedSchoolId) {
            return null;
        }

        return School::query()->find($this->selectedSchoolId);
    }

    public function getLevels(): Collection
    {
        if (! $this->hasSelectedSchool()) {
            return collect();
        }

        if ($this->selectedSchoolId === self::EMPTY_SCHOOL) {
            return collect([[
                'value' => self::EMPTY_LEVEL,
                'label' => 'Kelas belum diisi',
                'count' => User::query()->whereNull('school_id')->count(),
            ]]);
        }

        $levels = Classroom::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('is_active', true)
            ->withCount('users')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Classroom $classroom): array => [
                'value' => (string) $classroom->id,
                'label' => 'Kelas '.$classroom->name,
                'count' => $classroom->users_count,
            ])
            ->values();

        $usersWithoutClass = User::query()
            ->where('school_id', $this->selectedSchoolId)
            ->whereNull('classroom_id')
            ->count();

        if ($usersWithoutClass > 0) {
            $levels->push([
                'value' => self::EMPTY_LEVEL,
                'label' => 'Kelas belum diisi',
                'count' => $usersWithoutClass,
            ]);
        }

        return $levels;
    }

    public function getSelectedLevelLabel(): string
    {
        if ($this->selectedLevel === self::EMPTY_LEVEL) {
            return 'Kelas belum diisi';
        }

        $classroomName = Classroom::query()
            ->whereKey($this->selectedLevel)
            ->where('school_id', $this->selectedSchoolId)
            ->value('name');

        return $classroomName ? 'Kelas '.$classroomName : 'Kelas tidak ditemukan';
    }

    public function getSelectedSchoolLabel(): string
    {
        if ($this->selectedSchoolId === self::EMPTY_SCHOOL) {
            return 'Sekolah belum diisi';
        }

        return $this->getSelectedSchool()?->name ?? 'Belum dipilih';
    }

    public function selectSchool(int $schoolId): void
    {
        $schoolExists = $schoolId === self::EMPTY_SCHOOL
            ? User::query()->whereNull('school_id')->exists()
            : School::query()->whereKey($schoolId)->exists();

        abort_unless($schoolExists, 404);

        $this->selectedSchoolId = $schoolId;
        $this->selectedLevel = null;
        $this->resetTable();
    }

    public function selectLevel(string $level): void
    {
        $availableLevels = $this->getLevels()->pluck('value');

        abort_unless($availableLevels->contains($level), 404);

        $this->selectedLevel = $level;
        $this->resetTable();
    }

    public function backToSchools(): void
    {
        $this->selectedSchoolId = null;
        $this->selectedLevel = null;
        $this->resetTable();
    }

    public function backToLevels(): void
    {
        $this->selectedLevel = null;
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery()->with('school');

        if (! $this->hasSelectedSchool() || blank($this->selectedLevel)) {
            return $query->whereKey(-1);
        }

        if ($this->selectedSchoolId === self::EMPTY_SCHOOL) {
            $query->whereNull('school_id');
        } else {
            $query->where('school_id', $this->selectedSchoolId);
        }

        if ($this->selectedLevel === self::EMPTY_LEVEL) {
            return $query->whereNull('classroom_id');
        }

        return $query->where('classroom_id', $this->selectedLevel);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah pengguna')
                ->icon('heroicon-o-plus'),
        ];
    }

    private function hasSelectedSchool(): bool
    {
        if ($this->selectedSchoolId === null) {
            return false;
        }

        if ($this->selectedSchoolId === self::EMPTY_SCHOOL) {
            return User::query()->whereNull('school_id')->exists();
        }

        return $this->getSelectedSchool() !== null;
    }
}
