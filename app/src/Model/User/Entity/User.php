<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

class User
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @var Id
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     */
    private $date;
    /**
     * @var Email|null
     */
    private $email;
    /**
     * @var string|null
     */
    private $passwordHash;
    /**
     * @var Name
     */
    private $name;
    /**
     * @var string
     */
    private $status;
    /**
     * @var Role
     */
    private $role;
    /**
     * @var string|null
     */
    private $confirmToken;
    /**
     * @var ResetToken|null
     */
    private $resetToken;

    private function __construct(Id $id, \DateTimeImmutable $date, Email $email, string $hash, Name $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->name = $name;
        $this->role = Role::user();
    }

    // ##### CONSTRUCTORS #####

    public static function create(Id $id, \DateTimeImmutable $date, Email $email, string $hash, Name $name): self
    {
        $user = new self($id, $date, $email, $hash, $name);
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    public static function signUpRequest(Id $id, \DateTimeImmutable $date, Email $email, string $hash, Name $name, string $token): self
    {
        $user = new self($id, $date, $email, $hash, $name);
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;
        return $user;
    }

    // ##### LOGIC #####

    public function signUpConfirm(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public function edit(Email $email, Name $name): void
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function activate(): void
    {
        if ($this->isWait()) {
            throw new \DomainException('User is not confirmed.');
        }
        if ($this->isActive()) {
            throw new \DomainException('User is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    public function block(): void
    {
        if ($this->isWait()) {
            throw new \DomainException('User is not confirmed.');
        }
        if ($this->isBlocked()) {
            throw new \DomainException('User is already blocked.');
        }
        $this->status = self::STATUS_BLOCKED;
    }

    public function passwordResetRequest(ResetToken $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }
        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $hash;
        $this->resetToken = null;
    }

    // ##### IS METHODS #####

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    // ##### GETTERS #####

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }
}
