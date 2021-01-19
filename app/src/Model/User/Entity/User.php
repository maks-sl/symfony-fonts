<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

class User
{
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

    private function __construct(Id $id, \DateTimeImmutable $date, Email $email, string $hash, Name $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->name = $name;
    }

    public static function create(Id $id, \DateTimeImmutable $date, Email $email, string $hash, Name $name): self
    {
        return new self($id, $date, $email, $hash, $name);
    }

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
}
