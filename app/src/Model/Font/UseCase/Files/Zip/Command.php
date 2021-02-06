<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Files\Zip;

use App\Model\Font\Service\File\AddedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @var AddedFile
     */
    public $file;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
