<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\Flusher;
use App\Model\User\Entity\Email;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\Tokenizer;
use App\Model\User\Service\Sender;

class Handler
{
    private $users;
    private $tokenizer;
    private $flusher;
    private $sender;

    public function __construct(
        UserRepository $users,
        Tokenizer $tokenizer,
        Flusher $flusher,
        Sender $sender
    )
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->passwordResetRequest(
            $this->tokenizer->generateResetToken(),
            new \DateTimeImmutable()
        );

        $this->flusher->flush();

        $this->sender->sendResetToken($user->getEmail(), $user->getResetToken());
    }
}
