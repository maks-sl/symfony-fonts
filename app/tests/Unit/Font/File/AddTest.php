<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\File;

use App\Model\Font\Entity\File\File;
use App\Model\Font\Entity\File\Id;
use App\Model\Font\Entity\File\Info;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    public function testSuccess(): void
    {
        $font = (new FontBuilder())->build();

        $font->addFile(
            $date = new \DateTimeImmutable('+5 minutes'),
            $id = Id::next(),
            $info = new Info('path', 'font-regular', 'eot', 256)
        );

        self::assertCount(1, $files = $font->getFiles());
        self::assertInstanceOf(File::class, $file = end($files));

        self::assertEquals($id, $file->getId());
        self::assertEquals($date, $file->getDate());
        self::assertEquals($info, $file->getInfo());
        self::assertEquals($date, $font->getFilesUpdatedAt());
    }

    public function testAlready(): void
    {
        $font = (new FontBuilder())->build();

        $font->addFile(
            $date = new \DateTimeImmutable(),
            $id = Id::next(),
            $info = new Info('path', 'font-regular', 'eot', 256)
        );

        $this->expectExceptionMessage('Filename is already exists.');

        $font->addFile(
            $date = new \DateTimeImmutable(),
            $id = Id::next(),
            $info = new Info('path', 'font-regular', 'eot', 256)
        );
    }
}