<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\File;

use App\Model\Font\Entity\File\File;
use App\Model\Font\Entity\File\Id;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class FindTest extends TestCase
{
    public function testByFilename(): void
    {
        $font = (new FontBuilder())
            ->withFile($id = Id::next(), $name = 'font-regular', $ext = 'eot')
            ->withFile(Id::next(), 'font-italic', 'eot')
            ->build();

        $file = $font->findFile($name, $ext);
        self::assertInstanceOf(File::class, $file);
        self::assertEquals($id, $file->getId());

        self::assertNull($font->findFile('not-existing', 'ttf'));
    }

    public function testByExtension(): void
    {
        $ext = 'eot';
        $font = (new FontBuilder())
            ->withFile(Id::next(), $name = 'font-regular',$ext )
            ->withFile(Id::next(), 'font-italic', $ext)
            ->withFile(Id::next(), 'font-bold', $ext)
            ->build();

        self::assertCount(3, $font->findFilesByExt($ext));
        self::assertCount(0, $font->findFilesByExt('not-existing'));
    }
}