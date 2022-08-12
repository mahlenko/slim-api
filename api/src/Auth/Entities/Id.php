<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    /**
     * Id constructor.
     * @param string $value
     */
    public function __construct(private string $value)
    {
        Assert::notEmpty($this->value);
        Assert::uuid($this->value);
    }

    /**
     * @return self
     */
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
