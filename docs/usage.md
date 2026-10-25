# Usage and options

```php
$panel->plugin(SsoPlugin::make());
```

adds, to that panel:

| What | How |
|---|---|
| A **Sign in with Microsoft 365** button below the login form | render hook `AUTH_LOGIN_FORM_AFTER`; the password form stays |
| The portal's session rules on every panel request, Livewire updates included | `EnsureSsoSession` as persistent auth middleware |
| The browser guard on every page: deadline timer, heartbeat, Livewire 401 hook | render hook `BODY_END` |
| Signing out of a portal session returns to the launchpad | binds Filament's `LogoutResponse` contract |
| Landing on the panel after the callback | registers the panel as the core package's home URL |

A user who signs in with a password has no portal session, so none of the session rules touch them, and signing out sends
them to the panel's login page as before.

## Options

```php
SsoPlugin::make()
    ->redirectLogin()               // the login page sends everyone to the portal
    ->allowLocalLogin(false)        // with redirectLogin(): no ?local=1 password form either
    ->loginButton(false)            // no button
    ->buttonLabel('Sign in with Contoso')
    ->browserGuard(false)           // no deadline timer / heartbeat script
    ->logoutToPortal(false)         // signing out stays on the panel
```

| Option | Default | |
|---|---|---|
| `redirectLogin()` | off | Opening the panel's login page redirects to the portal. `?local=1` still shows the password form, the way in when the portal is down. Falls back to the normal form when single sign-on is not set up, so a missing `.env` value never locks anyone out. The button is hidden when everyone is redirected. |
| `allowLocalLogin(false)` | `true` | With `redirectLogin()`: remove the `?local=1` escape. Only do this when the portal is the only way in. |
| `loginButton(false)` | `true` | Hide the button (use with `redirectLogin()`, or when you place the link yourself). |
| `buttonLabel($label)` | translation | A string or closure. The default text is `postiefs-sso::messages.sign_in_with_microsoft`. |
| `browserGuard(false)` | `true` | Without it an idle tab is signed out only on its next request, and a Livewire action may show an error instead of going to the launchpad. |
| `logoutToPortal(false)` | `true` | Keep your own `LogoutResponse` binding. |

`redirectLogin()` is a middleware on the panel (`RedirectLoginToPortal`), not a replacement login page, so the panel's
login route, its "intended URL" and its sign-out route are untouched.

## Two ways to run

**Both logins side by side (the default).** People choose the button or the password form. Good while some users still
have only a password, or while you roll single sign-on out.

**Portal only.** `->redirectLogin()` (and `->allowLocalLogin(false)` once you trust the portal). Create the first admin
with a seeder or `php artisan tinker` before turning the escape off.

## The button

The view is `postiefs-sso-filament::login-button` and uses `<x-filament::button>`. Publish it to change the markup:

```bash
php artisan vendor:publish --tag=postiefs-sso-filament-views
```

The text comes from the core package's translations (`php artisan vendor:publish --tag=postiefs-sso-lang`).

## Several panels

Add the plugin to each panel that should accept the portal. After the callback people land on the **default** panel
(`->default()`), or on `SSO_HOME` / `postiefs-sso.home` if set. A page asked for before signing in is restored first.

## Reading the portal session

Anywhere in the app, including in `canAccessPanel()` or a Filament policy:

```php
use Postiefs\Sso\Facades\Sso;

Sso::active();           // signed in through the portal
Sso::hasRole('editor');  // the portal's claims; authorise with your app's own roles (e.g. Spatie)
Sso::secondsRemaining();
```

## Events

Everything from the core package: `SsoUserCreated`, `SsoLoggedIn`, `SsoSessionEnded`. Show a Filament notification when
the portal ended a session, for example, by listening to `SsoSessionEnded` (the user is already signed out by then, so
store the message in the session flash for the login page).
