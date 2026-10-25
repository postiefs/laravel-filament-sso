<?php

namespace Postiefs\SsoFilament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Postiefs\Sso\SsoServiceProvider;
use Postiefs\SsoFilament\SsoFilamentServiceProvider;
use Postiefs\SsoFilament\Tests\Fixtures\AdminPanelProvider;
use Postiefs\SsoFilament\Tests\Fixtures\User;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends Orchestra
{
    public const SECRET = 'a-shared-secret-of-at-least-32-bytes!!';

    /**
     * Whether the panel under test sends the login page to the portal (`SsoPlugin::redirectLogin()`).
     */
    protected bool $redirectLogin = false;

    protected function getPackageProviders($app): array
    {
        // Providers a Filament version does not have are left out: v3 has no Schemas, and the Blade capture directive
        // is a dependency of some versions only.
        $optional = [
            BladeCaptureDirectiveServiceProvider::class,
            SchemasServiceProvider::class,
        ];

        return [
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            ...array_values(array_filter($optional, 'class_exists')),
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            SsoServiceProvider::class,
            SsoFilamentServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('sso-filament-test.redirect_login', $this->redirectLogin);

        $app['config']->set('postiefs-sso.app_id', 'inventory');
        $app['config']->set('postiefs-sso.secret', self::SECRET);
        $app['config']->set('postiefs-sso.url', 'http://portal.test');
        $app['config']->set('postiefs-sso.public_url', 'https://sso.example.com');
        $app['config']->set('postiefs-sso.portal_url', 'https://portal.example.com');
        $app['config']->set('postiefs-sso.user.model', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }
}
