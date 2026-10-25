# Changelog

All notable changes to this package are documented here, newest first.

## [Unreleased]

## [1.0.0]

- `SsoPlugin` for Filament 3, 4 and 5 panels on Laravel 11, 12 and 13.
- A "Sign in with Microsoft 365" button below the login form; `redirectLogin()` / `allowLocalLogin()` to send everyone to the
  portal (a panel middleware, no login page is replaced).
- The portal's session rules on every panel request, Livewire updates included; the browser guard on every page.
- Signing out of a portal session returns to the launchpad (the sign-out response contract of Filament 3 or 4/5 is picked
  automatically).
- After the callback, users land on the default panel.
