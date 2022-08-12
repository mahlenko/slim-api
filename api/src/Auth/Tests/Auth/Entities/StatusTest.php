<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class StatusTest extends TestCase
{
    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Status('incorrect status');
    }

    public function testFill(): void
    {
        $status = new Status(Status::WAIT);
        $this->assertNotEmpty($status->getValue());
    }

    public function testWait(): void
    {
        $status = Status::wait();
        $this->assertTrue($status->isWait());
    }

    public function testConfirmed(): void
    {
        $status = Status::confirmed();
        $this->assertTrue($status->isConfirmed());
    }

    public function testBlocked(): void
    {
        $status = Status::blocked();
        $this->assertTrue($status->isBlocked());
    }
}
