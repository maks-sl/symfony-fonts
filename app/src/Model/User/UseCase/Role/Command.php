<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

use App\Model\User\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @Assert\Choice(callback={"App\Model\User\Entity\Role", "choices"})
     * @Assert\NotBlank()
     */
    public $role;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUser(User $user): self
    {
        $command = new self($user->getId()->getValue());
        $command->role = $user->getRole()->getName();
        return $command;
    }

}
