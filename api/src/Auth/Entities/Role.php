<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use Webmozart\Assert\Assert;

class Role
{
    /**
     * Administrator
     */
    public const ADMIN = 'admin';

    /**
     * Administrator
     */
    public const USER = 'user';

    /**
     * @var string
     */
    private string $value;

    /**
     * Role constructor.
     * @param string $role
     */
    public function __construct(string $role)
    {
        Assert::oneOf($role, [
            self::ADMIN,
            self::USER,
        ]);

        $this->value = trim($role);
    }

    /**
     * @param string $role
     * @return self
     */
    public static function set(string $role): self
    {
        return new self($role);
    }

    /**
     * @return self
     */
    public static function admin(): self
    {
        return self::set(self::ADMIN);
    }

    /**
     * @return self
     */
    public static function user(): self
    {
        return self::set(self::USER);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getValue() === self::ADMIN;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->getValue() === self::USER;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
