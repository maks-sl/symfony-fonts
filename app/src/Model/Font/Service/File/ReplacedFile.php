<?php

declare(strict_types=1);

namespace App\Model\Font\Service\File;

class ReplacedFile
{
    private $id;
    private $path;
    private $name;
    private $ext;
    private $size;

    public function __construct(string $id, string $path, string $name, string $ext, int $size)
    {
        $this->id = $id;
        $this->path = $path;
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
    }

    public function getId(): string
    {
        return $this->id;
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
}
