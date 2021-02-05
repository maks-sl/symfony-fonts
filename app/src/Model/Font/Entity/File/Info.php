<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\File;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Info
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $path;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $ext;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $size;

    public function __construct(string $path, string $name, string $ext, int $size)
    {
        $this->path = $path;
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function isFileNameSame(self $other): bool
    {
        return $this->getName() === $other->getName()
            && $this->getExt() === $other->getExt();
    }
}
