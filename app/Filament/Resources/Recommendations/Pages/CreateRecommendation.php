<?php

namespace App\Filament\Resources\Recommendations\Pages;

use App\Filament\Resources\Recommendations\RecommendationResource;
use App\Filament\Resources\Recommendations\Schemas\RecommendationForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;
use Illuminate\Contracts\Support\Htmlable;

class CreateRecommendation extends CreateRecord
{
    use HasWizard {
        getWizardComponent as getBaseWizardComponent;
    }

    protected static string $resource = RecommendationResource::class;

    protected static bool $canCreateAnother = false;

    protected array $extraBodyAttributes = [
        'class' => 'mentari-content-form-page mentari-recommendation-form-page',
    ];

    public function getTitle(): string|Htmlable
    {
        return 'Buat Rekomendasi';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Ikuti langkah singkat ini agar rekomendasi otomatis mudah dipahami siswa dan rapi dikelola admin.';
    }

    public function getBreadcrumb(): string
    {
        return 'Baru';
    }

    public function getSteps(): array
    {
        return RecommendationForm::wizardSteps();
    }

    public function getWizardComponent(): Component
    {
        /** @var Wizard $wizard */
        $wizard = $this->getBaseWizardComponent();

        return $wizard
            ->label('Tahapan pembuatan rekomendasi')
            ->extraAttributes(['class' => 'mentari-content-wizard mentari-recommendation-wizard'])
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
            ->label('Simpan rekomendasi')
            ->icon('heroicon-o-check-circle');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Batal')
            ->icon('heroicon-o-x-mark');
    }
}
