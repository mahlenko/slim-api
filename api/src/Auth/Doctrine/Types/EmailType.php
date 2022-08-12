<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Types;

use App\Auth\Entities\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EmailType extends StringType
{
    public const NAME = 'account_email';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Email|mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /* @var Email $value */
        return $value instanceof Email
            ? $value->getValue()
            : $value;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Email|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value)
            ? new Email($value)
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
