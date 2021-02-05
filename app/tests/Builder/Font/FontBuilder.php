<?php

declare(strict_types=1);

namespace App\Tests\Builder\Font;

use App\Model\Font\Entity\File\Info;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\Font;
use App\Model\Font\Entity\File\Id as FileId;
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
    private $filesData;

    public function __construct()
    {
        $this->id = Id::next();
        $this->slug = 'test-slug';
        $this->name = 'Font Name';
        $this->author = 'Font Author';
        $this->license = License::free();
        $this->languages = [Language::latin(), Language::cyrillic()];
        $this->filesData = [];
    }

    public function withFile(FileId $id, string $name, string $ext): self
    {
        $clone = clone $this;
        $clone->filesData[] = [
            'id' => $id,
            'name' => $name,
            'ext' => $ext,
        ];
        return $clone;
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

        foreach ($this->filesData as $data) {
            $font->addFile(
                new \DateTimeImmutable(),
                $data['id'],
                new Info(
                    $data['id']->getValue(),
                    $data['name'],
                    $data['ext'],
                    random_int(50, 500)
                )
            );
        }

        return $font;
    }
}
