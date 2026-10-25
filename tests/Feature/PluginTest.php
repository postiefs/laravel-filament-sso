<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Postiefs\Sso\Http\Middleware\EnsureSsoSession;
use Postiefs\Sso\Testing\LaunchToken;
use Postiefs\SsoFilament\FilamentCompat;
use Postiefs\SsoFilament\Tests\Fixtures\User;

it('adds a Microsoft 365 button below the login form', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('Sign in with Microsoft 365')
        ->assertSee(route('sso.login'), false);
});

it('hides the button until single sign-on is set up', function () {
    config(['postiefs-sso.secret' => null]);

    $this->get('/admin/login')->assertOk()->assertDontSee('Sign in with Microsoft 365');
});

it('runs the portal session rules on every panel request', function () {
    expect(Filament::getDefaultPanel()->getAuthMiddleware())->toContain(EnsureSsoSession::class);
});

it('signs in through the portal and lands on the panel', function () {
    $this->get('/sso/callback?sso_token='.LaunchToken::make(['email' => 'jane@example.com']))
        ->assertRedirect(url('/admin'));

    expect(Auth::check())->toBeTrue();
});

it('forwards a token that landed on the panel to the callback', function () {
    $this->get('/admin?sso_token=abc')->assertRedirect(url('sso/callback').'?sso_token=abc');
});

it('adds the browser guard to the panel pages of a portal session', function () {
    $this->get('/sso/callback?sso_token='.LaunchToken::make(['sexp' => time() + 3600]));

    $this->get('/admin')->assertOk()->assertSee(addcslashes(route('sso.heartbeat'), '/'), false);
});

it('returns to the portal when a portal session signs out', function () {
    $this->get('/sso/callback?sso_token='.LaunchToken::make());

    $this->post('/admin/logout')->assertRedirect('https://portal.example.com');
});

it('signs a local login out to the panel login page as before', function () {
    $this->actingAs(User::query()->forceCreate(['name' => 'Local', 'email' => 'local@example.com', 'password' => 'x']));

    $this->post('/admin/logout')->assertRedirect(Filament::getLoginUrl());
});

it('uses its own logout response', function () {
    expect(app(FilamentCompat::logoutResponseContract()))->toBeInstanceOf(FilamentCompat::logoutResponse());
});
