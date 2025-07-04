<?php

namespace App\Providers\Filament;

use App\Filament\Pages\SshCommandRunner;
use App\Filament\Pages\SshSettings;
use App\Filament\Widgets\SshStatsWidget;
use App\Http\Middleware\DesktopAuthenticate;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
        $panel = $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                SshCommandRunner::class,
                SshSettings::class,
            ])
            ->widgets([
                SshStatsWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
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
            ]);

        // Apply authentication based on mode
        if (config('app.desktop_mode', false)) {
            $panel->authMiddleware([
                DesktopAuthenticate::class,
            ]);
        } elseif (config('app.dev_no_auth', false) && config('app.env') === 'local') {
            // Skip authentication entirely in dev mode
            $panel->authMiddleware([]);
        } else {
            $panel->authMiddleware([
                Authenticate::class,
            ]);
        }

        return $panel;
    }
}
