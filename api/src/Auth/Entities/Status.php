<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use Webmozart\Assert\Assert;

class Status
{
    /**
     * Ожидает подтверждения
     * @var string
     */
    public const WAIT = 'wait';

    /**
     * Подтвержденный
     * @var string
     */
    public const CONFIRMED = 'confirmed';

    /**
     * Заблокированный
     * @var string
     */
    public const BLOCKED = 'blocked';

    /**
     * Status constructor.
     * @param string $value
     */
    public function __construct(private string $value)
    {
        Assert::oneOf($this->value, [
            self::WAIT,
            self::CONFIRMED,
            self::BLOCKED,
        ]);
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->getValue() === self::WAIT;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->getValue() === self::CONFIRMED;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->getValue() === self::BLOCKED;
    }

    /**
     * @return self
     */
    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    /**
     * @return self
     */
    public static function confirmed(): self
    {
        return new self(self::CONFIRMED);
    }

    /**
     * @return self
     */
    public static function blocked(): self
    {
        return new self(self::BLOCKED);
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
