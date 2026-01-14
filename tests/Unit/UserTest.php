<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function it_checks_if_user_is_admin()
    {
        $admin = new User(['role_id' => 1]);
        $client = new User(['role_id' => 2]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($client->isAdmin());
    }

    /** @test */
    public function it_checks_if_user_is_manager()
    {
        $manager = new User(['role_id' => 3]);
        $client = new User(['role_id' => 2]);

        $this->assertTrue($manager->isManager());
        $this->assertFalse($client->isManager());
    }
}
