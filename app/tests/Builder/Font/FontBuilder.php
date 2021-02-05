<?php

declare(strict_types=1);

namespace App\Tests\Builder\Font;

use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\Font;
use App\Model\Font\Entity\Language;
use App\Model\Font\Entity\License;

class FontBuilder
{
    private $id;
    private $slug;
    private $name;
    private $author;
    private $license;
    private $languages;

    public function __construct()
    {
        $this->id = Id::next();
        $this->slug = 'test-slug';
        $this->name = 'Font Name';
        $this->author = 'Font Author';
        $this->license = License::free();
        $this->languages = [Language::latin(), Language::cyrillic()];
    }


    public function build(): Font
    {
        $font = new Font(
            $this->id,
            new \DateTimeImmutable(),
            $this->slug,
            $this->name,
            $this->author,
            $this->license,
            $this->languages
        );

        return $font;
    }
}
