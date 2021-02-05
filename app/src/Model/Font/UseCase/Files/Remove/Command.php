<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @Assert\NotBlank()
     */
    public $file;

    public function __construct(string $id, string $file)
    {
        $this->id = $id;
        $this->file = $file;
    }
}
