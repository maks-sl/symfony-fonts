<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\ResetToken;
use Ramsey\Uuid\Uuid;

class Tokenizer
{
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function generateResetToken(): ResetToken
    {
        return new ResetToken(
            Uuid::uuid4()->toString(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }

}
