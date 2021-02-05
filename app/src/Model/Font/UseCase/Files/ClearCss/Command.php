<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\ClearCss;

use App\Model\Font\Service\File\ReplacedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @var ReplacedFile[]
     */
    public $files;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
