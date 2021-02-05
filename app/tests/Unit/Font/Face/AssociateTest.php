<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\Face;

use App\Model\Font\Entity\File\Id;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class AssociateTest extends TestCase
{
    public function testHasFaces(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font-regular', 'eot')
            ->build();

        self::assertFalse($font->hasFaces());

        $font->associateFaces();

        self::assertTrue($font->hasFaces());
    }

    public function testNumFaces(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font-regular', 'eot')
            ->withFile(Id::next(), 'font-regular', 'ttf')
            ->withFile(Id::next(), 'font-regular', 'woff')
            ->withFile(Id::next(), 'font-italic', 'eot')
            ->build();

        $font->associateFaces();

        self::assertCount(2, $font->getFaces());
    }

    public function testFaceName(): void
    {
        $name = 'font-regular';
        $font = (new FontBuilder())
            ->withFile(Id::next(), $name, 'eot')
            ->withFile(Id::next(), $name, 'ttf')
            ->withFile(Id::next(), $name, 'woff')
            ->build();

        $font->associateFaces();

        $face = current($font->getFaces());
        self::assertEquals($name, $face->getName());
    }

    public function testNumFiles(): void
    {
        $name = 'font-regular';
        $font = (new FontBuilder())
            ->withFile(Id::next(), $name, 'eot')
            ->withFile(Id::next(), $name, 'ttf')
            ->withFile(Id::next(), $name, 'woff')
            ->build();

        $font->associateFaces();

        $face = current($font->getFaces());
        self::assertCount(3, $face->getFiles());
    }

    public function testRemove(): void
    {
        $font = (new FontBuilder())
            ->withFile($id = Id::next(), 'font-regular', 'eot')
            ->build();

        $font->associateFaces();
        $font->removeFile(new \DateTimeImmutable(), $id);
        $font->associateFaces();

        self::assertEmpty($font->getFaces());
    }
}