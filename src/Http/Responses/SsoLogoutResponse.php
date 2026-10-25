<?php

namespace Postiefs\SsoFilament\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Postiefs\SsoFilament\Http\Responses\Concerns\ReturnsToPortal;

/**
 * The sign-out response for Filament 4 and 5.
 */
class SsoLogoutResponse implements LogoutResponse
{
    use ReturnsToPortal;
}
