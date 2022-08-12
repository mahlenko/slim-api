<?php

declare(strict_types=1);

namespace Auth\Services;

use App\Auth\Services\Tokenizer;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Services
 */
class TokenizerTest extends TestCase
{

    public function testSuccess(): void
    {
        $interval = new DateInterval('PT1H');
        $date = new DateTimeImmutable('+1 day');

        $tokenizer = new Tokenizer($interval);
        $token = $tokenizer->generate($date);

        $this->assertEquals($date->add($interval), $token->getExpires());
    }
}
