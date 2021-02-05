<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\File;

use App\Model\Font\Entity\File\File;
use App\Model\Font\Entity\File\Id;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    public function testSuccess(): void
    {
        $font = (new FontBuilder())
            ->withFile($id = Id::next(), 'font-regular', 'eot')
            ->withFile(Id::next(), 'font-italic', 'eot')
            ->build();

        $file = $font->getFile($id);
        self::assertInstanceOf(File::class, $file);
        self::assertEquals($id, $file->getId());
    }

    public function testNotFound(): void
    {
        $font = (new FontBuilder())->build();

        $this->expectExceptionMessage('File is not found.');
        $font->getFile(Id::next());
    }
}