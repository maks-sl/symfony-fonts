<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\File;

use App\Model\Font\Entity\Face\Face;
use App\Model\Font\Entity\Font;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="font_files", indexes={
 *     @ORM\Index(columns={"date"})
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"info_path", "info_name", "info_ext"})
 * })
 */


class File
{
    /**
     * @var Font
     * @ORM\ManyToOne(targetEntity="App\Model\Font\Entity\Font", inversedBy="files")
     * @ORM\JoinColumn(name="font_id", referencedColumnName="id", nullable=false)
     */
    private $font;
    /**
     * @var Face
     * @ORM\ManyToOne(targetEntity="App\Model\Font\Entity\Face\Face", inversedBy="files")
     * @ORM\JoinColumn(name="face_id", referencedColumnName="id")
     */
    private $face;
    /**
     * @var Id
     * @ORM\Column(type="font_file_id")
     * @ORM\Id
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;
    /**
     * @var Info
     * @ORM\Embedded(class="Info")
     */
    private $info;

    public function __construct(Font $font, Id $id, \DateTimeImmutable $date, Info $info)
    {
        $this->font = $font;
        $this->id = $id;
        $this->date = $date;
        $this->info = $info;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }

    public function assignFace(Face $face): void
    {
        $this->face = $face;
    }
}
