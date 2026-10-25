<?php

namespace Postiefs\SsoFilament;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Postiefs\Sso\Support\HomeUrl;

class SsoFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'postiefs-sso-filament');

        // After signing in through the portal, users land on the default panel.
        $this->app->make(HomeUrl::class)->resolveUsing(fn (): ?string => Filament::getDefaultPanel()?->getUrl());

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../resources/views' => resource_path('views/vendor/postiefs-sso-filament')], 'postiefs-sso-filament-views');
        }
    }
}
