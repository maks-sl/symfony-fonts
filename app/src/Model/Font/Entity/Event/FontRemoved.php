<?php

declare(strict_types=1);

namespace App\Model\Font\Entity\Event;

use App\Model\Font\Entity\Id;

class FontRemoved
{
    public $id;

    public function __construct(Id $id)
    {
        $this->id = $id;
    }
}
