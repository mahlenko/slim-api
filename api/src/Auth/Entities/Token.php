<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable()
 * @package App\Auth\Entities
 */
class Token
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private string $value;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $expires;

    /**
     * Token constructor.
     * @param string $value
     * @param DateTimeImmutable $expires
     */
    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::notEmpty($value);

        $this->value = $value;
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    /**
     * @param DateTimeImmutable $date
     * @return bool
     */
    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->getExpires() <= $date;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isEqualTo(string $value): bool
    {
        return $this->getValue() === $value;
    }

    /**
     * @param string $value
     * @param DateTimeImmutable $date
     */
    public function validate(string $value, DateTimeImmutable $date): void
    {
        if ($this->isExpiredTo($date)) {
            throw new DomainException('Token is expired.');
        }

        if (!$this->isEqualTo($value)) {
            throw new DomainException('Token is invalid.');
        }
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->getValue());
    }
}
