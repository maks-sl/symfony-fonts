<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\File;

use App\Model\Font\Entity\File\Id;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class HasTest extends TestCase
{
    public function testWithoutFiles(): void
    {
        $font = (new FontBuilder())->build();

        self::assertFalse($font->hasFile('not-existing', 'ext'));
        self::assertFalse($font->hasCss());
        self::assertFalse($font->hasZip());
    }

    public function testFile(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), $name = 'font-regular', $ext = 'eot')
            ->build();

        self::assertTrue($font->hasFile($name, $ext));
    }

    public function testCss(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'style', 'css')
            ->build();

        self::assertTrue($font->hasCss());
    }

    public function testZip(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font', 'zip')
            ->build();

        self::assertTrue($font->hasZip());
    }
}