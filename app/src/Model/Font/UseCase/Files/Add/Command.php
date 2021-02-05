<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\Add;

use Symfony\Component\Validator\Constraints as Assert;
use App\Model\Font\Service\File\AddedFile;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @var AddedFile[]
     */
    public $files;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
