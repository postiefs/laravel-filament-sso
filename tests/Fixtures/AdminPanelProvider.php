<?php

namespace Postiefs\SsoFilament\Tests\Fixtures;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Postiefs\SsoFilament\SsoPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Laravel 13 renamed the CSRF middleware.
        $csrf = class_exists(PreventRequestForgery::class)
            ? PreventRequestForgery::class
            : VerifyCsrfToken::class;

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->pages([Dashboard::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                $csrf,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class], isPersistent: true)
            ->plugin(SsoPlugin::make()->redirectLogin((bool) config('sso-filament-test.redirect_login')));
    }
}
