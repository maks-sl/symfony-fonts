<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font;

use App\Model\Font\Entity\Status;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class ChangeStatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $font = (new FontBuilder())->build();

        $font->changeStatus(
            $status = Status::active()
        );

        self::assertEquals($status, $font->getStatus());
        self::assertTrue($font->isActive());

        $font->changeStatus(
            $status = Status::hidden()
        );

        self::assertEquals($status, $font->getStatus());
        self::assertTrue($font->isHidden());
    }

    public function testAlready(): void
    {
        $font = (new FontBuilder())->build();

        $this->expectExceptionMessage('Status is already same.');
        $font->changeStatus(
            Status::hidden()
        );
    }
}
