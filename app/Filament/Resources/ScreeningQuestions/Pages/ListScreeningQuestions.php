<?php

namespace App\Filament\Resources\ScreeningQuestions\Pages;

use App\Filament\Resources\ScreeningQuestions\ScreeningQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ListScreeningQuestions extends ListRecords
{
    protected static string $resource = ScreeningQuestionResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Pertanyaan DASS-21';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Kelola teks, skala, urutan, dan status pertanyaan screening.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah pertanyaan')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah Pertanyaan DASS-21')
                ->modalSubmitActionLabel('Simpan pertanyaan')
                ->modalWidth(Width::Large)
                ->createAnother(false),
        ];
    }
}
