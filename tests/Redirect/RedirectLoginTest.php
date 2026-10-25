<?php

use Postiefs\SsoFilament\SsoPlugin;

it('sends the panel login page to the portal', function () {
    $this->get('/admin/login')->assertRedirect(route('sso.login'));
});

it('shows the password form on ?local=1', function () {
    $this->get('/admin/login?local=1')->assertOk()->assertSee('Sign in');
});

it('shows the form when single sign-on is not set up, so nobody is locked out', function () {
    config(['postiefs-sso.secret' => null]);

    $this->get('/admin/login')->assertOk();
});

it('does not touch other panel pages', function () {
    $this->get('/admin')->assertRedirect(url('/admin/login'));
});

it('has no login button when everyone is sent to the portal', function () {
    $this->get('/admin/login?local=1')->assertOk()->assertDontSee('Sign in with Microsoft 365');
});

it('registers the options on the plugin', function () {
    expect(SsoPlugin::make()->redirectLogin()->shouldRedirectLogin())->toBeTrue()
        ->and(SsoPlugin::make()->shouldAllowLocalLogin())->toBeTrue()
        ->and(SsoPlugin::make()->allowLocalLogin(false)->shouldAllowLocalLogin())->toBeFalse();
});
