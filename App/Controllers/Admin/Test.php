<?php

namespace App\Controllers\Admin;

class Users extends \Core\Controller
{
    public function testAction()
    {
        echo "<h1>Admin\Users::test</h1>";
    }

    /**
     * Before filter - called before an action methods
     */
    protected function before()
    {
        // ex: Make sure an admin user is logged in
        return true;
    }

    /**
     * After filter - called after an action methods
     */
    protected function after()
    {}

}