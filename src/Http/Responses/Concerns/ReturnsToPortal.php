<?php

namespace Postiefs\SsoFilament\Http\Responses\Concerns;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Livewire\Features\SupportRedirects\Redirector;
use Postiefs\Sso\Contracts\PortalClient;
use Postiefs\Sso\Events\SsoSessionEnded;
use Postiefs\Sso\SsoSession;

/**
 * The sign-out response, shared by the Filament 3 and the Filament 4/5 contract: signing out of a portal session returns
 * to the launchpad; a local login goes to the panel's login page as before.
 */
trait ReturnsToPortal
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (app(SsoSession::class)->signedOut()) {
            event(new SsoSessionEnded(null, 'logout'));

            return redirect()->away(app(PortalClient::class)->portalUrl());
        }

        return redirect()->to(Filament::getLoginUrl());
    }
}
