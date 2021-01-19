<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->build();

        $user->signUpConfirm();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->build();

        $user->signUpConfirm();
        $this->expectExceptionMessage('User is already confirmed.');
        $user->signUpConfirm();
    }

}
