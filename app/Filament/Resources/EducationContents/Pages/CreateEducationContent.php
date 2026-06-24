<?php

namespace App\Filament\Resources\EducationContents\Pages;

use App\Filament\Resources\EducationContents\EducationContentResource;
use App\Filament\Resources\EducationContents\Schemas\EducationContentForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;
use Illuminate\Contracts\Support\Htmlable;

class CreateEducationContent extends CreateRecord
{
    use HasWizard {
        getWizardComponent as getBaseWizardComponent;
    }

    protected static string $resource = EducationContentResource::class;

    protected static bool $canCreateAnother = false;

    protected array $extraBodyAttributes = [
        'class' => 'mentari-content-form-page',
    ];

    public function getTitle(): string|Htmlable
    {
        return 'Buat Konten Edukasi';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Ikuti 3 langkah berikut. Pengaturan opsional dapat dilewati dan dilengkapi nanti.';
    }

    public function getBreadcrumb(): string
    {
        return 'Baru';
    }

    public function getSteps(): array
    {
        return EducationContentForm::wizardSteps();
    }

    public function getWizardComponent(): Component
    {
        /** @var Wizard $wizard */
        $wizard = $this->getBaseWizardComponent();

        return $wizard
            ->label('Tahapan pembuatan konten')
            ->extraAttributes(['class' => 'mentari-content-wizard'])
            ->nextAction(fn (Action $action): Action => $action
                ->label('Lanjut')
                ->icon('heroicon-o-arrow-right'))
            ->previousAction(fn (Action $action): Action => $action
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left'));
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan konten')
            ->icon('heroicon-o-check-circle');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Batal')
            ->icon('heroicon-o-x-mark');
    }
}
