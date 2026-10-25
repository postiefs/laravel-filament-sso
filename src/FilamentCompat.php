<?php

namespace Postiefs\SsoFilament;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Postiefs\SsoFilament\Http\Responses\SsoLogoutResponse;
use Postiefs\SsoFilament\Http\Responses\SsoLogoutResponseV3;

/**
 * The few places Filament 3 differs from 4 and 5.
 */
final class FilamentCompat
{
    /**
     * Filament 4 and 5 moved the sign-out response contract; Filament 3 has it under `Http\Responses\Auth`.
     *
     * @return class-string
     */
    public static function logoutResponseContract(): string
    {
        return interface_exists(LogoutResponse::class)
            ? LogoutResponse::class
            : \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class;
    }

    /**
     * @return class-string
     */
    public static function logoutResponse(): string
    {
        return interface_exists(LogoutResponse::class)
            ? SsoLogoutResponse::class
            : SsoLogoutResponseV3::class;
    }
}
