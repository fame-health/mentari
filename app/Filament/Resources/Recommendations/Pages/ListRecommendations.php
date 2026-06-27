<?php

namespace App\Filament\Resources\Recommendations\Pages;

use App\Filament\Resources\Recommendations\RecommendationResource;
use App\Filament\Resources\Recommendations\Tables\RecommendationsTable;
use App\Models\Recommendation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class ListRecommendations extends ListRecords
{
    protected static string $resource = RecommendationResource::class;

    protected string $view = 'filament.resources.recommendations.pages.list-recommendations-by-category';

    #[Url(as: 'category')]
    public ?string $selectedCategory = null;

    public function mount(): void
    {
        parent::mount();

        if ($this->selectedCategory !== null && ! $this->hasSelectedCategory()) {
            $this->selectedCategory = null;
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Rekomendasi';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Pilih jenis rekomendasi terlebih dahulu untuk melihat daftar datanya.';
    }

    public function getCurrentStep(): int
    {
        return $this->hasSelectedCategory() ? 2 : 1;
    }

    public function getCategories(): Collection
    {
        $summary = Recommendation::query()
            ->select('category')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN is_active THEN 1 ELSE 0 END) as active_count')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        return collect(Recommendation::CATEGORY_LABELS)
            ->map(fn (string $label, string $category): array => [
                'value' => $category,
                'label' => $label,
                'icon' => $this->categoryIcon($category),
                'tone' => str_replace('_', '-', $category),
                'count' => (int) ($summary->get($category)?->total_count ?? 0),
                'active_count' => (int) ($summary->get($category)?->active_count ?? 0),
            ])
            ->values();
    }

    public function getSelectedCategoryLabel(): string
    {
        if (! $this->hasSelectedCategory()) {
            return 'Belum dipilih';
        }

        return Recommendation::CATEGORY_LABELS[$this->selectedCategory];
    }

    public function getSelectedCategoryCount(): int
    {
        if (! $this->hasSelectedCategory()) {
            return 0;
        }

        return Recommendation::query()
            ->where('category', $this->selectedCategory)
            ->count();
    }

    public function selectCategory(string $category): void
    {
        abort_unless(array_key_exists($category, Recommendation::CATEGORY_LABELS), 404);

        $this->selectedCategory = $category;
        $this->resetTable();
    }

    public function backToCategories(): void
    {
        $this->selectedCategory = null;
        $this->resetTable();
    }

    public function hasSelectedCategory(): bool
    {
        return $this->selectedCategory !== null
            && array_key_exists($this->selectedCategory, Recommendation::CATEGORY_LABELS);
    }

    public function table(Table $table): Table
    {
        return RecommendationsTable::configure($table, includeCategoryFilter: false);
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if (! $this->hasSelectedCategory()) {
            return $query->whereKey(-1);
        }

        return $query->where('category', $this->selectedCategory);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah rekomendasi')
                ->icon('heroicon-o-plus'),
        ];
    }

    private function categoryIcon(string $category): string
    {
        return match ($category) {
            Recommendation::COUNSELING_SCRIPT_CATEGORY => 'heroicon-o-chat-bubble-left-right',
            Recommendation::DASHBOARD_ANALYSIS_CATEGORY => 'heroicon-o-chart-bar-square',
            'relaxation' => 'heroicon-o-sparkles',
            'reflection' => 'heroicon-o-pencil-square',
            'activity' => 'heroicon-o-bolt',
            default => 'heroicon-o-light-bulb',
        };
    }
}
