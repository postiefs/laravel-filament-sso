<?php

namespace Postiefs\SsoFilament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Postiefs\Sso\Contracts\PortalClient;
use Postiefs\SsoFilament\SsoPlugin;
use Symfony\Component\HttpFoundation\Response;

/**
 * Added to a panel by `SsoPlugin::redirectLogin()`. Opening the panel's login page sends the browser to the portal
 * instead, except with `?local=1` (the way in when the portal is down) unless the plugin turned that off.
 *
 * A middleware rather than a replacement login page, so it works the same on Filament 3, 4 and 5 and keeps the panel's
 * own login route, "intended" URL and sign-out route.
 */
class RedirectLoginToPortal
{
    public function __construct(private PortalClient $portal) {}

    public function handle(Request $request, Closure $next): Response
    {
        $panelId = $this->loginPanelId($request);

        if ($panelId === null || ! $request->isMethod('GET') || ! $this->portal->enabled()) {
            return $next($request);
        }

        $plugin = Filament::getPanel($panelId)->getPlugin(SsoPlugin::ID);

        if (! $plugin instanceof SsoPlugin || ! $plugin->shouldRedirectLogin()) {
            return $next($request);
        }

        if ($plugin->shouldAllowLocalLogin() && $request->boolean('local')) {
            return $next($request);
        }

        return redirect()->route('sso.login');
    }

    /**
     * The id of the panel whose login page this is, from the route name `filament.{panel}.auth.login`.
     */
    private function loginPanelId(Request $request): ?string
    {
        $name = (string) $request->route()?->getName();

        if (preg_match('/^filament\.(.+)\.auth\.login$/', $name, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }
}
