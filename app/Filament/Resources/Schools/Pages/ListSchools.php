<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah sekolah')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah sekolah')
                ->modalDescription('Cukup isi nama dan alamat. Kode sekolah dibuat otomatis oleh sistem.')
                ->modalSubmitActionLabel('Simpan sekolah')
                ->modalWidth(Width::ThreeExtraLarge)
                ->createAnother(false),
        ];
    }
}
