<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\Face;

use App\Model\Font\Entity\File\File;

use App\Model\Font\Entity\Font;
use Doctrine\Common\Collections\ArrayCollection;

class Face
{
    private $font;
    private $id;
    private $name;
    private $sort;
    private $files;

    public function __construct(
        Font $font,
        Id $id,
        string $name,
        int $sort
    )
    {
        $this->font = $font;
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->files = new ArrayCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    static function compareSort(self $a, self $b): int
    {
        return $a->getSort() <=> $b->getSort();
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files->toArray();
    }

    public function addFile(File $file): void
    {
        $this->files->add($file);
    }

    public function hasFile(File $file): bool
    {
        return $this->files->contains($file);
    }

}
