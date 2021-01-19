<?php

declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Name;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->build();
        $user->edit(
            $email = new Email('new-email@app.test'),
            $name = new Name('NewFirst', 'NewLast')
        );

        self::assertEquals($name, $user->getName());
        self::assertEquals($email, $user->getEmail());
    }
}
