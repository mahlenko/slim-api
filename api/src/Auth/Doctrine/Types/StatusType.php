<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Types;

use App\Auth\Entities\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class StatusType extends StringType
{

    public const NAME = 'account_status';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Status|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value)
            ? new Status($value)
            : null;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Status|mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /* @var Status $value */
        return $value instanceof Status
            ? $value->getValue()
            : $value;
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
