<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

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
}
