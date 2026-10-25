# postiefs/laravel-filament-sso

Filament plugin for the Postiefs SSO portal (Microsoft 365). It works on **Filament 3, 4 and 5** and **Laravel 11, 12 and 13**.

Everything about tokens, sessions, users and events lives in [`postiefs/laravel-sso`](https://github.com/postiefs/laravel-sso); this package adds the
panel parts:

- a **Sign in with Microsoft 365** button below the login form (the password login keeps working), or, if you prefer,
  everyone is sent to the portal
- the portal's session rules on every panel request, Livewire updates included: the portal's deadline, a session an
  administrator ended, access that was removed
- the browser guard on every page: an idle tab is signed out at the deadline, and a Livewire action that hits an ended
  session goes to the launchpad instead of showing an error
- **Sign out** of a portal session returns to the launchpad; a password login signs out as before
- after the callback, people land on the panel

## Quick start

```bash
composer require postiefs/laravel-filament-sso
php artisan sso:install
```

```php
use Postiefs\SsoFilament\SsoPlugin;

$panel->login()->plugin(SsoPlugin::make());
```

Set `SSO_APP_ID`, `SSO_JWT_SECRET`, `SSO_URL`, `SSO_PUBLIC_URL`, `SSO_PORTAL_URL` and `SESSION_LIFETIME=720` in `.env`,
ask the SSO team to register `https://your-app/sso/callback` as the Callback URL, then `php artisan sso:check`.
Nothing changes until the app id and the secret are set.

## Documentation

- [Installation](docs/installation.md): Composer repositories, the plugin, the `.env` keys, who may sign in, a checklist
- [Usage and options](docs/usage.md): what the plugin adds, `redirectLogin()`, `allowLocalLogin()`, the button, several panels
- [Compatibility](docs/compatibility.md): the supported versions and what differs between Filament 3, 4 and 5
- [Troubleshooting](docs/troubleshooting.md): a table from symptom to cause and fix
- [Testing](docs/testing.md): the package's own tests, and testing a panel that uses the plugin

The core package's docs cover the rest: [how it works](https://github.com/postiefs/laravel-sso/blob/main/docs/how-it-works.md),
[configuration](https://github.com/postiefs/laravel-sso/blob/main/docs/configuration.md), [users and roles](https://github.com/postiefs/laravel-sso/blob/main/docs/users-and-roles.md),
[reference](https://github.com/postiefs/laravel-sso/blob/main/docs/reference.md).

## Tests

```bash
composer install
vendor/bin/pest
```
