<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\File;

use App\Model\Font\Entity\File\Id;
use App\Model\Font\Entity\File\Info;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class ConsistentTest extends TestCase
{
    public function testWithoutFiles(): void
    {
        $font = (new FontBuilder())->build();
        self::assertFalse($font->isZipConsistent());
    }

    public function testWithoutZip(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font-regular', 'eot')
            ->withFile(Id::next(), 'style', 'css')
            ->build();

        self::assertFalse($font->isZipConsistent());
    }

    public function testWithZipOutDated(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font-regular', 'eot')
            ->withFile(Id::next(), 'style', 'css')
            ->build();

        $font->addFile(
            $date = new \DateTimeImmutable('-5 minutes'),
            $id = Id::next(),
            $info = new Info('path', 'font', 'zip', 256)
        );

        $font->addFile(
            $date = new \DateTimeImmutable(),
            $id = Id::next(),
            $info = new Info('path', 'font-italic', 'eot', 256)
        );

        self::assertFalse($font->isZipConsistent());
    }

    public function testSuccess(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), $name = 'font-regular', $ext = 'eot')
            ->withFile(Id::next(), $name = 'font-regular', $ext = 'ttf')
            ->withFile(Id::next(), $name = 'font-regular', $ext = 'woff')
            ->withFile(Id::next(), 'font-italic', 'eot')
            ->withFile(Id::next(), 'style', 'css')
            ->build();

        $font->addFile(
            $date = new \DateTimeImmutable('+5 minutes'),
            $id = Id::next(),
            $info = new Info('path', 'font', 'zip', 256)
        );

        self::assertTrue($font->isZipConsistent());
    }

}