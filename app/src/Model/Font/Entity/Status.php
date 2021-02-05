<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use Webmozart\Assert\Assert;

class Status
{
    public const ACTIVE = 'active';
    public const HIDDEN = 'hidden';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::arrayOfConst());
        $this->name = $name;
    }

    public static function arrayOfConst(): array
    {
        return [
            self::ACTIVE,
            self::HIDDEN,
        ];
    }

    public static function verbose(string $v): string
    {
        switch ($v) {
            case self::ACTIVE:
                return 'Active';
            case self::HIDDEN:
                return 'Hidden';
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

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function hidden(): self
    {
        return new self(self::HIDDEN);
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->name === self::ACTIVE;
    }

    public function isHidden(): bool
    {
        return $this->name === self::HIDDEN;
    }

}
