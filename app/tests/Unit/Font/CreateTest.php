<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font;

use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\Font;
use App\Model\Font\Entity\Language;
use App\Model\Font\Entity\License;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess(): void
    {

        $font = new Font(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $slug = 'test-slug',
            $name = 'Font Name',
            $author = 'Font Author',
            $license = License::free(),
            $languages = [Language::latin(), Language::cyrillic()]
        );

        self::assertEquals($id, $font->getId());
        self::assertEquals($date, $font->getDate());
        self::assertEquals($slug, $font->getSlug());
        self::assertEquals($name, $font->getName());
        self::assertEquals($author, $font->getAuthor());
        self::assertEquals($license, $font->getLicense());
        self::assertEquals($languages, $font->getLanguages());

        self::assertTrue($font->isHidden());
        self::assertFalse($font->isActive());

    }
}
