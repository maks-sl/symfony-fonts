<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';

    public const CHOICES_LIST = [
        'User' => self::USER,
        'Admin' => self::ADMIN,
    ];

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::choices());
        $this->name = $name;
    }

    public static function choices(): array
    {
        return array_values(self::CHOICES_LIST);
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public function isUser(): bool
    {
        return $this->name === self::USER;
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    public function isEqual(self $role): bool
    {
        return $this->getName() === $role->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
