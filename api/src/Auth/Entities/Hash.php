<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use App\Auth\Services\PasswordHashing;
use RuntimeException;
use Webmozart\Assert\Assert;

class Hash implements PasswordHashing
{
    public function __construct(private int $memory_cost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST)
    {
    }

    /**
     * @inheritDoc
     */
    public function hash(string $string): string
    {
        Assert::notEmpty($string);

        $hash = password_hash($string, PASSWORD_ARGON2I, [
            'memory_cost' => $this->memory_cost
        ]);

        if (!$hash) {
            throw new RuntimeException('Unable to generate hash');
        }

        return $hash;
    }

    /**
     * @inheritDoc
     */
    public function validate(string $string, string $hash): bool
    {
        return password_verify($string, $hash);
    }
}
