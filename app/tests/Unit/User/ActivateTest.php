<?php

declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->confirmed()->build();
        $user->block();
        $user->activate();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->confirmed()->build();

        $this->expectExceptionMessage('User is already active.');
        $user->activate();
    }

    public function testNotConfirmed(): void
    {
        $user = (new UserBuilder())->build();

        $this->expectExceptionMessage('User is not confirmed.');
        $user->activate();
    }
}
