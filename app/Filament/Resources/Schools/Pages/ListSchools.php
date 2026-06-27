<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Filament\Resources\Schools\Schemas\SchoolForm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Wizard;
use Filament\Support\Enums\Width;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    protected string $view = 'filament.resources.schools.pages.list-schools';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah sekolah')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah sekolah')
                ->modalDescription('Ikuti 2 langkah singkat: isi data sekolah, lalu tambahkan kelas yang tersedia.')
                ->modalSubmitActionLabel('Simpan sekolah')
                ->steps(SchoolForm::wizardSteps())
                ->modifyWizardUsing(fn (Wizard $wizard): Wizard => $wizard
                    ->label('Tahapan tambah sekolah')
                    ->extraAttributes(['class' => 'mentari-school-wizard'])
                    ->nextAction(fn (Action $action): Action => $action
                        ->label('Lanjut isi kelas')
                        ->icon('heroicon-o-arrow-right'))
                    ->previousAction(fn (Action $action): Action => $action
                        ->label('Kembali')
                        ->icon('heroicon-o-arrow-left')))
                ->modalWidth(Width::FourExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->createAnother(false),
        ];
    }
}
