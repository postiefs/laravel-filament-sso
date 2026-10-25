<?php

namespace Postiefs\SsoFilament;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Postiefs\Sso\Contracts\PortalClient;
use Postiefs\Sso\Http\Middleware\EnsureSsoSession;
use Postiefs\SsoFilament\Http\Middleware\RedirectLoginToPortal;

/**
 * Puts a Filament panel behind the SSO portal, next to its own login:
 *
 *     $panel->plugin(SsoPlugin::make())
 *
 *  - the login page gets a "Sign in with Microsoft 365" button below the form (or, with `redirectLogin()`, sends
 *    everyone to the portal and keeps the password form on `?local=1`)
 *  - the portal's session rules run on every panel request, Livewire updates included
 *  - the browser guard (deadline timer, heartbeat, Livewire 401 hook) is added to every page
 *  - signing out of a portal session returns to the launchpad; a local login signs out as before
 *
 * Nothing happens until SSO_APP_ID and SSO_JWT_SECRET are set.
 */
class SsoPlugin implements Plugin
{
    public const ID = 'sso';

    private bool $redirectLogin = false;

    private bool $allowLocalLogin = true;

    private bool $loginButton = true;

    private bool $browserGuard = true;

    private bool $logoutToPortal = true;

    private string|Closure|null $buttonLabel = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(self::ID);

        return $plugin;
    }

    public function getId(): string
    {
        return self::ID;
    }

    /**
     * Send everyone who opens the login page straight to the portal, instead of showing the password form with a
     * button below it. `?local=1` still shows the form unless `allowLocalLogin(false)`.
     */
    public function redirectLogin(bool $condition = true): static
    {
        $this->redirectLogin = $condition;

        return $this;
    }

    /**
     * With `redirectLogin()`: whether `?local=1` opens the password form (the way in when the portal is down).
     */
    public function allowLocalLogin(bool $condition = true): static
    {
        $this->allowLocalLogin = $condition;

        return $this;
    }

    public function loginButton(bool $condition = true): static
    {
        $this->loginButton = $condition;

        return $this;
    }

    public function buttonLabel(string|Closure|null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function browserGuard(bool $condition = true): static
    {
        $this->browserGuard = $condition;

        return $this;
    }

    public function logoutToPortal(bool $condition = true): static
    {
        $this->logoutToPortal = $condition;

        return $this;
    }

    public function shouldRedirectLogin(): bool
    {
        return $this->redirectLogin;
    }

    public function shouldAllowLocalLogin(): bool
    {
        return $this->allowLocalLogin;
    }

    public function register(Panel $panel): void
    {
        if ($this->redirectLogin) {
            $panel->middleware([RedirectLoginToPortal::class]);
        }

        // Persistent: it also runs on the Livewire update requests inside the panel.
        $panel->authMiddleware([EnsureSsoSession::class], isPersistent: true);

        if ($this->loginButton && ! $this->redirectLogin) {
            $panel->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => app(PortalClient::class)->enabled()
                    ? view('postiefs-sso-filament::login-button', ['label' => value($this->buttonLabel)])->render()
                    : '',
            );
        }

        if ($this->browserGuard) {
            $panel->renderHook(PanelsRenderHook::BODY_END, fn (): string => view('postiefs-sso::session-guard')->render());
        }

        if ($this->logoutToPortal) {
            app()->bind(FilamentCompat::logoutResponseContract(), FilamentCompat::logoutResponse());
        }
    }

    public function boot(Panel $panel): void {}
}
