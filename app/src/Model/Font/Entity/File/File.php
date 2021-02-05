<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\File;

use App\Model\Font\Entity\Font;

class File
{
    private $font;
    private $id;
    private $date;
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
}
