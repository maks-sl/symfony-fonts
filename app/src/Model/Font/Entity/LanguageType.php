<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class LanguageType extends JsonType
{
    public const NAME = 'font_languages';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof ArrayCollection) {
            $data = array_map([self::class, 'deserialize'], $value->toArray());
        } else {
            $data = $value;
        }

        return parent::convertToDatabaseValue($data, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!is_array($data = parent::convertToPHPValue($value, $platform))) {
            return $data;
        }

        return new ArrayCollection(array_filter(array_map([self::class, 'serialize'], $data)));
    }

    public function getName(): string
    {
        return self::NAME;
    }

    private static function deserialize(Language $language): string
    {
        return $language->getName();
    }

    private static function serialize(string $name): ?Language
    {
        return in_array($name, Language::arrayOfConst(), true) ? new Language($name) : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}
