<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Sort;

use App\Model\Font\Entity\Font;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @var string[]
     */
    public $faces;

    private function __construct(string $id, array $faces)
    {
        $this->id = $id;
        $this->faces = $faces;
    }

    public static function fromFont(Font $font): self
    {
        $command = new self(
            $font->getId()->getValue(),
            array_map(function ($face) {
                return $face->getId()->getValue();
            }, $font->getFaces())
        );
        return $command;
    }
}
