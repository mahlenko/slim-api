<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Hash;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class HashTest extends TestCase
{
    public function testHashing(): void
    {
        $hasher = new Hash(16);
        $hash = $hasher->hash($password = 'secret-password');

        $this->assertNotEquals($password, $hash);
    }

    public function testValid(): void
    {
        $hasher = new Hash(16);
        $hash = $hasher->hash($password = 'secret-password');

        $this->assertTrue($hasher->validate($password, $hash));
        $this->assertFalse($hasher->validate('wrong-password', $hash));
    }

    public function testEmpty(): void
    {
        $hasher = new Hash(16);
        $this->expectException(InvalidArgumentException::class);
        $hasher->hash('');
    }
}
