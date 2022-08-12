<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Token;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class TokenTest extends TestCase
{
    public function testValue(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            new DateTimeImmutable()
        );

        $this->assertTrue($token->isEqualTo($value));
    }

    public function testInvalidValue(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            new DateTimeImmutable()
        );

        $this->assertFalse($token->isEqualTo('invalid'));
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('', new DateTimeImmutable());
    }

    public function testExpires(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expire = new DateTimeImmutable()
        );

        $this->assertEquals($expire, $token->getExpires());
    }

    public function testValidExpire(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            (new DateTimeImmutable())->modify('+1 hour')
        );

        $this->assertFalse($token->isExpiredTo(new DateTimeImmutable()));
    }

    public function testValid(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $date = new DateTimeImmutable()
        );

        $this->assertFalse($token->isExpiredTo($date->modify('-1 secs')));
        $this->assertTrue($token->isExpiredTo($date));
        $this->assertTrue($token->isExpiredTo($date->modify('+1 secs')));
        $this->assertTrue($token->isEqualTo($value));
    }
}
