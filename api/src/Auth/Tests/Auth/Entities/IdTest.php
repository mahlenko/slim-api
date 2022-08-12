<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Id;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class IdTest extends TestCase
{
    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('123456');
    }

    public function testValid(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $this->assertEquals($uuid, (new Id($uuid))->getValue());
    }

    public function testGenerate(): void
    {
        $id = Id::generate();
        $this->assertNotEmpty($id->getValue());
    }
}
