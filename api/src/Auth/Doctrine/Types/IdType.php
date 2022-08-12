<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Types;

use App\Auth\Entities\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class IdType extends GuidType
{
    public const NAME = 'account_id';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Id|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return !empty($value)
            ? new Id((string) $value)
            : null;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        /* @var Id|string $value */
        return $value instanceof Id
            ? $value->getValue()
            : (string) $value;
    }

    /**
     * @param AbstractPlatform $platform
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
