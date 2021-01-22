<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\Email;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\User;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    private $users;
    private $hasher;
    private $flusher;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            $email,
            $this->hasher->hash($command->password),
            new Name(
                $command->firstName,
                $command->lastName
            )
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}
