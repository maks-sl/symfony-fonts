<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\SignUp;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\User;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::signUpRequest(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $email = new Email('test@app.test'),
            $hash = 'hash',
            $name = new Name('First', 'Last'),
            $token = 'token'
        );

        $user->signUpConfirm();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testAlready(): void
    {
        $user = User::signUpRequest(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $email = new Email('test@app.test'),
            $hash = 'hash',
            $name = new Name('First', 'Last'),
            $token = 'token'
        );

        $user->signUpConfirm();
        $this->expectExceptionMessage('User is already confirmed.');
        $user->signUpConfirm();
    }

}
