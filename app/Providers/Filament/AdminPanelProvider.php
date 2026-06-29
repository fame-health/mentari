<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\MentariStatsOverview;
use App\Filament\Widgets\MoodTrendChart;
use App\Filament\Widgets\RecentRiskAlerts;
use App\Filament\Widgets\RiskLevelChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('MENTARI')
            ->colors([
                'primary' => Color::Pink,
                'danger' => Color::Red,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->navigationGroups([
                $this->mentariNavigationGroup('M (Mood Tracking Harian)', 'M', 'mood'),
                $this->mentariNavigationGroup('E (Edukasi Kesehatan Mental)', 'E', 'education'),
                $this->mentariNavigationGroup('N (Notifikasi Risiko Dini)', 'N', 'risk'),
                $this->mentariNavigationGroup('T (Tes Screening DASS-21)', 'T', 'screening'),
                $this->mentariNavigationGroup('A (Analisis Data/Digital Dashboard)', 'A', 'analytics'),
                $this->mentariNavigationGroup('R (Rekomendasi Personalisasi)', 'R', 'recommendation'),
                $this->mentariNavigationGroup('I (Integrasi Komunitas Sekolah)', 'I', 'community'),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                MentariStatsOverview::class,
                MoodTrendChart::class,
                RiskLevelChart::class,
                RecentRiskAlerts::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function mentariNavigationGroup(string $label, string $step, string $tone): NavigationGroup
    {
        return NavigationGroup::make($label)
            ->extraSidebarAttributes([
                'class' => "mentari-nav-group mentari-nav-group--{$tone}",
                'data-mentari-step' => $step,
            ]);
    }
}
