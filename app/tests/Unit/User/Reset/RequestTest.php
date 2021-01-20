<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Reset;

use App\Model\User\Entity\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->confirmed()->build();

        $user->passwordResetRequest($token, $now);

        self::assertNotNull($user->getResetToken());
    }

    public function testAlready(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->confirmed()->build();

        $user->passwordResetRequest($token, $now);

        $this->expectExceptionMessage('Resetting is already requested.');
        $user->passwordResetRequest($token, $now);
    }

    public function testWhenExpired(): void
    {
        $now = new \DateTimeImmutable();

        $user = (new UserBuilder())->confirmed()->build();

        $token1 = new ResetToken('token', $now->modify('+1 day'));
        $user->passwordResetRequest($token1, $now);

        self::assertEquals($token1, $user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 day'));
        $user->passwordResetRequest($token2, $now->modify('+2 day'));

        self::assertEquals($token2, $user->getResetToken());
    }

    public function testNotActive(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->build();

        $this->expectExceptionMessage('User is not active.');
        $user->passwordResetRequest($token, $now);
    }

}
