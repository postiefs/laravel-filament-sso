<?php

namespace Postiefs\SsoFilament\Tests\Redirect;

use Postiefs\SsoFilament\Tests\TestCase;

/**
 * A panel whose plugin has `redirectLogin()` on.
 */
abstract class RedirectTestCase extends TestCase
{
    protected bool $redirectLogin = true;
}
