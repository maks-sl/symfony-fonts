<?php

declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\User;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::create(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $email = new Email('test@app.test'),
            $hash = 'hash',
            $name = new Name('First', 'Last')
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($name, $user->getName());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
    }
}
