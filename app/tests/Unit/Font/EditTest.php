<?php

declare(strict_types=1);

namespace App\Tests\Unit\Font;

use App\Model\Font\Entity\Language;
use App\Model\Font\Entity\License;
use App\Tests\Builder\Font\FontBuilder;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    public function testSuccess(): void
    {

        $font = (new FontBuilder())->build();

        $font->edit(
            $slug = 'new-slug',
            $name = 'New Name',
            $author = 'New Author',
            $license = License::free(),
            $languages = [Language::latin(), Language::cyrillic()]
        );

        self::assertEquals($slug, $font->getSlug());
        self::assertEquals($name, $font->getName());
        self::assertEquals($author, $font->getAuthor());
        self::assertEquals($license, $font->getLicense());
        self::assertEquals($languages, $font->getLanguages());
    }

}
