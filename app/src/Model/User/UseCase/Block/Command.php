<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Block;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
