<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Ramsey\Uuid\Uuid;

class Tokenizer
{
    private $interval;

    public function __construct()
    {
        $this->interval = new \DateInterval('PT1H');
    }

    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

}
