<?php

namespace Postiefs\SsoFilament\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Postiefs\SsoFilament\Http\Responses\Concerns\ReturnsToPortal;

/**
 * The sign-out response for Filament 3. Only loaded there: the contract it implements does not exist in later versions.
 */
class SsoLogoutResponseV3 implements LogoutResponse
{
    use ReturnsToPortal;
}
