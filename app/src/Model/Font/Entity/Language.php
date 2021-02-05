<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use Webmozart\Assert\Assert;

class Language
{
    public const CYRILLIC = 'cyrillic';
    public const LATIN = 'latin';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::arrayOfConst());
        $this->name = $name;
    }

    public static function arrayOfConst(): array
    {
        return [
            self::CYRILLIC,
            self::LATIN,
        ];
    }

    public static function verbose(string $v): string
    {
        switch ($v) {
            case self::CYRILLIC:
                return 'Cyrillic';
            case self::LATIN:
                return 'Latin';
            default:
                return '';
        }
    }

    public static function choices(): array
    {
        $result = [];
        foreach (self::arrayOfConst() as $const) {
            $result[self::verbose($const)] = $const;
        }
        return $result;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function cyrillic(): self
    {
        return new self(self::CYRILLIC);
    }

    public static function latin(): self
    {
        return new self(self::LATIN);
    }
}
