<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Edit;

use App\Model\Flusher;
use App\Model\User\Entity\Email;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\UserRepository;

class Handler
{
    private $users;
    private $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $user->edit(
            new Email($command->email),
            new Name(
                $command->firstName,
                $command->lastName
            )
        );

        $this->flusher->flush();
    }
}
