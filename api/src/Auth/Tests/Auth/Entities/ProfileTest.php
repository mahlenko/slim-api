<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Profile;
use App\Auth\Entities\User;
use App\Auth\Tests\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class ProfileTest extends TestCase
{
    public function testSetName(): void
    {
        $profile = new Profile();
        $profile->setName($name = 'test name');
        $this->assertEquals($name, $profile->getName());
    }

    public function testSetUser(): void
    {
        $user = (new UserBuilder())->build();
        $profile = new Profile();
        $profile->setUser($user);

        $this->assertEquals($user, $profile->getUser());
    }
}
