# Troubleshooting

Start with `php artisan sso:check` and the `[sso]` lines of the log
(`grep "\[sso" storage/logs/laravel.log | tail -30`; `SSO_DEBUG=true` logs every step). The core package's
[troubleshooting page](https://github.com/postiefs/laravel-sso/blob/main/docs/troubleshooting.md) covers a sign-in that does nothing, tokens that are
rejected, and sessions that end too early or never. This page is about the panel.

| Symptom | Cause | Fix |
|---|---|---|
| No "Sign in with Microsoft 365" button | `SSO_APP_ID` or `SSO_JWT_SECRET` empty, config cached, or `loginButton(false)` / `redirectLogin()` | `php artisan config:clear`; check `sso:check` |
| Signing in at the portal lands on the panel login page | The Callback URL registered in the portal is the site root or the panel | Register `https://your-app/sso/callback`. With the default `stray_token.forward` the package forwards the token anyway and logs which page it arrived at |
| Signing out goes to the panel login, not the launchpad | The user signed in with a password (expected), or `logoutToPortal(false)`, or another provider binds Filament's `LogoutResponse` after the plugin | Bind order: the plugin binds when the panel is registered; remove the other binding |
| A Livewire action shows an error modal after the portal ended the session | The browser guard is off (`browserGuard(false)`), or the plugin is not on this panel | Keep the guard on; register the plugin on every panel |
| An idle tab stays open after the deadline | Same: no browser guard on the page | Same |
| `redirectLogin()` sends you in a loop | `SSO_URL` / `SSO_PUBLIC_URL` points back at this app | The public URL must be the portal's |
| Locked out after turning the password form off | `allowLocalLogin(false)` with the portal down | Re-enable `?local=1`, or sign in through the launchpad |
| A signed-in user gets a 403 in the panel | `canAccessPanel()` returns false for them | A resolver `isAllowed()` refusal shows the 403 at the callback instead; this one is Filament's own check |
| "No hint path defined for [postiefs-sso-filament]" | The service provider is not registered | `composer dump-autoload`; it is auto-discovered |

## What the plugin changes in the panel

Useful when something else in the panel behaves oddly:

- one **persistent auth middleware**: `Postiefs\Sso\Http\Middleware\EnsureSsoSession`
- with `redirectLogin()`, one **panel middleware**: `RedirectLoginToPortal` (only acts on `filament.{panel}.auth.login`)
- two **render hooks** on `AUTH_LOGIN_FORM_AFTER` and `BODY_END`
- one **container binding**: Filament's `LogoutResponse`
- the core package also registers a **global middleware** (`HandleStrayToken`) and the `sso/*` routes outside the panel
