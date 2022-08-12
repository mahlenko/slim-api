<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Types;

use App\Auth\Entities\Phone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class PhoneType extends StringType
{
    public const NAME = 'account_phone';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /* @var Phone $value */
        return $value instanceof Phone
            ? $value->getValue()
            : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value)
            ? new Phone($value, 'RU')
            : null;
    }

    /**
     * @param AbstractPlatform $platform
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
