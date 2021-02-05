<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font\Face;

use App\Model\Font\Entity\Face\Face;
use App\Model\Font\Entity\File\Id;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SortTest extends TestCase
{
    public function testSuccess(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font-regular', 'eot')
            ->withFile(Id::next(), 'font-italic', 'eot')
            ->withFile(Id::next(), 'font-bold', 'eot')
            ->build();

        $font->associateFaces();

        $reversedIds = array_reverse(
            array_map(function (Face $face) {
                return $face->getId()->getValue();
            }, $font->getFaces())
        );

        $font->sortFaces($reversedIds);

        $sortedIds = array_map(function (Face $face) {
            return $face->getId()->getValue();
        }, $font->getFaces());

        self::assertSame($reversedIds, $sortedIds);
    }

    public function testInconsistent(): void
    {
        $font = (new FontBuilder())
            ->withFile(Id::next(), 'font-regular', 'eot')
            ->withFile(Id::next(), 'font-italic', 'eot')
            ->withFile(Id::next(), 'font-bold', 'eot')
            ->build();

        $font->associateFaces();
        $this->expectExceptionMessage('Ids list is inconsistent.');

        $font->sortFaces([Uuid::uuid4()->toString()]);
    }
}