<?php

declare(strict_types=1);

namespace App\Model\User\Service\Factory;

use App\Model\User\Service\Tokenizer;

class TokenizerFactory
{
    public function create(string $interval): Tokenizer
    {
        return new Tokenizer(new \DateInterval($interval));
    }
}