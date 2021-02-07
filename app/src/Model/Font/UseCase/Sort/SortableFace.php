<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Sort;

class SortableFace
{
    public $id;
    public $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
