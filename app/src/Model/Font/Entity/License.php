<?php

declare(strict_types=1);

namespace App\Model\Font\Entity;

use Webmozart\Assert\Assert;

class License
{
    public const FREE = 'free';
    public const PERSONAL = 'personal';
    public const PAID = 'paid';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::arrayOfConst());
        $this->name = $name;
    }

    public static function arrayOfConst(): array
    {
        return [
            self::FREE,
            self::PERSONAL,
            self::PAID,
        ];
    }

    public static function verbose(string $v): string
    {
        switch ($v) {
            case self::FREE:
                return 'Free';
            case self::PERSONAL:
                return 'Personal';
            case self::PAID:
                return 'Paid';
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

    public static function free(): self
    {
        return new self(self::FREE);
    }

    public static function personal(): self
    {
        return new self(self::PERSONAL);
    }

    public static function paid(): self
    {
        return new self(self::PAID);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isFree(): bool
    {
        return $this->name === self::FREE;
    }

    public function isPersonal(): bool
    {
        return $this->name === self::PERSONAL;
    }

    public function isPaid(): bool
    {
        return $this->name === self::PAID;
    }

}
