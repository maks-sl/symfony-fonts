<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\Face;

use App\Model\Font\Entity\File\File;

use App\Model\Font\Entity\Font;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="font_faces", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"font_id", "sort"}),
 *     @ORM\UniqueConstraint(columns={"font_id", "name"})
 * })
 */
class Face
{
    /**
     * @var Font
     * @ORM\ManyToOne(targetEntity="App\Model\Font\Entity\Font", inversedBy="faces")
     * @ORM\JoinColumn(name="font_id", referencedColumnName="id", nullable=false)
     */
    private $font;
    /**
     * @ORM\Column(type="font_face_id")
     * @ORM\Id
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $sort;
    /**
     * @var ArrayCollection|File[]
     * @ORM\OneToMany(targetEntity="App\Model\Font\Entity\File\File", mappedBy="face")
     * @ORM\OrderBy({"date" = "ASC"})
     */
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
