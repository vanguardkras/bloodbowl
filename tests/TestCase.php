<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @return User
     */
    protected function loginAsFakeUser()
    {
        $user = User::create(['username' => 'TestUserForTests']);
        $this->actingAs($user);
        return $user;
    }
}
