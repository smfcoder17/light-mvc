<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;

class Users extends Controller
{
    public function testAction(): void
    {
        echo "<h1>Admin\Users::test</h1>";
    }

    /**
     * Before filter - called before an action methods
     */
    protected function before(): mixed
    {
        // ex: Make sure an admin user is logged in
        return true;
    }

    /**
     * After filter - called after an action methods
     */
    protected function after(): void {}
}
