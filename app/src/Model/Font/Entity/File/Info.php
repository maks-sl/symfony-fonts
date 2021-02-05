<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\File;

class Info
{
    private $path;
    private $name;
    private $ext;
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
