# Compatibility

| | Supported |
|---|---|
| PHP | 8.2, 8.3, 8.4, 8.5 |
| Laravel | 11, 12, 13 |
| Filament | 3, 4, 5 |

The package works the same on all three Filament versions. The places they differ are handled inside it, so your panel code
does not change when you upgrade Filament:

| | Filament 3 | Filament 4 and 5 | How the package copes |
|---|---|---|---|
| Sign-out response contract | `Filament\Http\Responses\Auth\Contracts\LogoutResponse` | `Filament\Auth\Http\Responses\Contracts\LogoutResponse` | picks the one that exists (`FilamentCompat`), with one response class per contract and a shared trait |
| Login page class | `Filament\Pages\Auth\Login` | `Filament\Auth\Pages\Login` | not needed: `redirectLogin()` is a middleware, no login page is subclassed |
| Everything else the plugin touches | | | the same in every version: `Plugin`, `Panel::authMiddleware(..., isPersistent: true)`, `PanelsRenderHook::AUTH_LOGIN_FORM_AFTER` and `BODY_END`, `<x-filament::button>` |

Filament 3 itself needs Laravel 10 or 11; the core package needs Laravel 11+, so on Filament 3 use Laravel 11.

## Tested combinations

The whole test suite was run on PHP 8.5 against these installs:

| Laravel | Filament | Result |
|---|---|---|
| 13 | 5 | passes |
| 12 | 4 | passes |
| 11 | 3 | passes, with PHP 8.5 deprecation notices raised by Laravel 11 and Filament 3 themselves (neither supports PHP 8.5 officially) |

The core package's own tests were run on Laravel 11 and 13, and on 12 through the Filament package's install. See [Testing](testing.md) for how to run
another combination.

## Upgrading Filament

Nothing to change in the app. If a Filament upgrade changes the sign-out contract again, the failure is a single failing
test (`it uses its own logout response`) and one method, `FilamentCompat`.
