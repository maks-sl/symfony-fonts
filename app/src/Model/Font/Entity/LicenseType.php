<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class LicenseType extends StringType
{
    public const NAME = 'font_license';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof License ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new License($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}