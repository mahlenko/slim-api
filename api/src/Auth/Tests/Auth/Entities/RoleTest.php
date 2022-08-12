<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Role;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class RoleTest extends TestCase
{
    public function testSet(): void
    {
        $this->assertTrue(Role::set(Role::ADMIN)->isAdmin());
    }

    public function testFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Role::set('invalid');
    }

    public function testAdmin(): void
    {
        $this->assertTrue(Role::admin()->isAdmin());
    }

    public function testUser(): void
    {
        $this->assertTrue(Role::user()->isUser());
    }

    public function testValue(): void
    {
        $this->assertEquals(Role::USER, Role::user()->getValue());
    }
}
