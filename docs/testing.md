# Testing

## The package's own tests

```bash
composer install
vendor/bin/pest
```

They use Orchestra Testbench with a small panel (`tests/Fixtures/AdminPanelProvider.php`) and an in-memory SQLite database;
nothing needs a running portal. `tests/Feature` runs against a panel with the default plugin, `tests/Redirect` against one
with `redirectLogin()` on.

To try another Laravel or Filament version, in a copy of the package folders (so your `vendor` stays as it is):

```bash
composer update -W --with "filament/filament:^3.0" --with "laravel/framework:^11.0" \
    --with "orchestra/testbench:^9.0" --with "pestphp/pest:^3.0" --with "pestphp/pest-plugin-laravel:^3.0"
vendor/bin/pest
```

(Add `--no-security-blocking` for Laravel 11, which Composer blocks for known advisories now that it is end of life.)

## Testing a panel that uses the plugin

Sign in the way the portal does, with the core package's `LaunchToken` helper (see its
[testing docs](https://github.com/postiefs/laravel-sso/blob/main/docs/testing.md)):

```php
use Postiefs\Sso\Testing\LaunchToken;

config([
    'postiefs-sso.app_id' => 'my-app',
    'postiefs-sso.secret' => 'a-shared-secret-of-at-least-32-bytes!!',
]);

$this->get('/sso/callback?sso_token='.LaunchToken::make(['email' => 'jane@example.com']))
    ->assertRedirect('/admin');

$this->get('/admin')->assertOk();
```

Check the login page offers the button:

```php
$this->get('/admin/login')->assertSee('Sign in with Microsoft 365');
```

Check the portal ending a session signs the user out of the panel:

```php
Http::fake(['portal.test/*' => Http::response(['allowed' => false, 'sessionValid' => false, 'reason' => 'revoked'])]);
config(['postiefs-sso.url' => 'http://portal.test']);

$this->get('/sso/callback?sso_token='.LaunchToken::make());
$this->travel(2)->minutes();

$this->get('/admin')->assertRedirect(route('sso.ended'));
```

Call `Filament::setCurrentPanel('admin')` (v3: `Filament::setCurrentPanel(Filament::getPanel('admin'))`) first when your test
renders panel pages with Livewire's `Livewire::test()`.
