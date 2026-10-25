# Installation

Requirements: PHP 8.2+, Laravel 11, 12 or 13, Filament 3, 4 or 5. Read the
[`postiefs/laravel-sso` docs](https://github.com/postiefs/laravel-sso/blob/main/docs/installation.md) for the portal side (what to ask the SSO team for,
the Callback URL, deploying); this page only adds the panel.

## 1. Install

```bash
composer require postiefs/laravel-filament-sso     # also installs postiefs/laravel-sso
php artisan sso:install                            # config, .env keys, the Callback URL to register
```

Until the packages are listed on Packagist, add both GitHub repositories to the app's `composer.json` (a private repository needs a GitHub token in Composer's `auth.json`):

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/postiefs/laravel-sso" },
    { "type": "vcs", "url": "https://github.com/postiefs/laravel-filament-sso" }
]
```

For local development, point `path` repositories at your checkouts instead, with `"options": { "symlink": true }`.

## 2. Register the plugin on the panel

```php
use Postiefs\SsoFilament\SsoPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->login()
        ->plugin(SsoPlugin::make());
}
```

The panel needs `->login()` (Filament's own login page). With several panels, add the plugin to each one that should accept
the portal.

## 3. Configure

Set the `.env` keys from the core package (`SSO_APP_ID`, `SSO_JWT_SECRET`, `SSO_URL`, `SSO_PUBLIC_URL`, `SSO_PORTAL_URL`,
`SESSION_LIFETIME=720`) and have the SSO team register `https://your-app/sso/callback` as the app's Callback URL. Then:

```bash
php artisan config:clear
php artisan sso:check
```

Until `SSO_APP_ID` and `SSO_JWT_SECRET` are set the plugin does nothing: no button, no checks, the panel behaves as before.

## 4. Decide who may sign in

The first time someone signs in through the portal, the core package creates a local user. Two things nearly every
Filament app wants:

**Refuse users the app has switched off**, and **give new users a starting role.** Both are in the core package's
[Users and roles](https://github.com/postiefs/laravel-sso/blob/main/docs/users-and-roles.md) docs. In short:

```php
// config/postiefs-sso.php (published by sso:install)
'user' => [
    'resolver' => App\Sso\AppUserResolver::class,       // extends DefaultUserResolver, overrides isAllowed()
    'defaults' => ['status' => 'active'],               // attributes of a created user
    // ...keep the rest of the block as published
],

// a service provider
Event::listen(SsoUserCreated::class, fn ($event) => $event->user->assignRole('Team Member'));
```

If the user model implements `Filament\Models\Contracts\FilamentUser`, `canAccessPanel()` keeps working as before: a user
who is signed in but may not use the panel still gets Filament's 403.

## Checklist

- [ ] `composer require` done, `php artisan sso:check` all green
- [ ] the plugin is on every panel that should accept the portal
- [ ] the Callback URL is registered exactly as printed by `sso:install`
- [ ] `SESSION_LIFETIME` is 720 or more on the server
- [ ] a test sign-in works from the launchpad **and** the panel's password login still works
