<?php

namespace App\Filament\Resources\Recommendations\Pages;

use App\Filament\Resources\Recommendations\RecommendationResource;
use App\Models\Recommendation;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewRecommendation extends ViewRecord
{
    protected static string $resource = RecommendationResource::class;

    protected array $extraBodyAttributes = [
        'class' => 'mentari-recommendation-view-page',
    ];

    public function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }

    public function getSubheading(): string|Htmlable|null
    {
        /** @var Recommendation $record */
        $record = $this->record;
        $category = Recommendation::CATEGORY_LABELS[$record->category] ?? $record->category;
        $severity = $record->severity ? Recommendation::SEVERITY_LABELS[$record->severity] : null;

        return $severity ? $category.' untuk status '.$severity : $category;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit rekomendasi')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
