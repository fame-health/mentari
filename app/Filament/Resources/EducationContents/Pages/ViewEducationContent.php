<?php

namespace App\Filament\Resources\EducationContents\Pages;

use App\Filament\Resources\EducationContents\EducationContentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ViewEducationContent extends ViewRecord
{
    protected static string $resource = EducationContentResource::class;

    protected Width|string|null $maxContentWidth = Width::ScreenTwoExtraLarge;

    public function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $category = $this->record->category?->title ?? 'Tanpa kategori';
        $type = match ($this->record->type) {
            'article' => 'Artikel',
            'infographic' => 'Infografis',
            'video' => 'Video',
            default => ucfirst($this->record->type),
        };

        return $category.' · '.$type;
    }

    public function getBreadcrumb(): string
    {
        return 'Detail';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke daftar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(EducationContentResource::getUrl('index')),
            EditAction::make()
                ->label('Edit konten')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
