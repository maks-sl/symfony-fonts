<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\File;

use App\Model\Font\Entity\File\Id;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class RemoveTest extends TestCase
{
    public function testSuccess(): void
    {
        $font = (new FontBuilder())
            ->withFile($id = Id::next(), 'font-regular', 'eot')
            ->build();

        $font->removeFile($date = new \DateTimeImmutable('+5 minutes'), $id);

        self::assertCount(0, $font->getFiles());
        self::assertEquals($date, $font->getFilesUpdatedAt());
    }

    public function testNotFound(): void
    {
        $font = (new FontBuilder())->build();

        $this->expectExceptionMessage('File is not found.');
        $font->removeFile(new \DateTimeImmutable(), Id::next());
    }
}