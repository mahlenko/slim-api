<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Types;

use App\Auth\Entities\Role;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class RoleType extends StringType
{
    /**
     * Named type
     */
    public const NAME = 'account_role';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /* @var Role $value */
        return $value instanceof Role
            ? $value->getValue()
            : (string) $value;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Role|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return empty($value)
            ? Role::set($value)
            : null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param AbstractPlatform $platform
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
