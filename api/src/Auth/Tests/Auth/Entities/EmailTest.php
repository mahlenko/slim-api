<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class EmailTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email(' ');
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('incorrect email');
    }

    public function testValid(): void
    {
        $email = new Email($value = 'demo@domain.com');
        $this->assertEquals($value, $email->getValue());
    }
}
