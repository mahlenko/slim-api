<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use Webmozart\Assert\Assert;

class Email
{
    /**
     * @var string
     */
    private string $value;

    /**
     * Email constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = trim($value);

        Assert::notEmpty($this->value);
        Assert::email($this->value);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isEqualTo(string $email): bool
    {
        return $this->getValue() === trim($email);
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
