<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Edit;

use App\Model\Font\Entity\Font;
use App\Model\Font\Entity\Language;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]+(?:-[a-z0-9]+)*$/",
     *     message="Use only [a-z], [0-9] and [-] symbols"
     * )
     */
    public $slug;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $author;
    /**
     * @Assert\Choice(callback={"App\Model\Font\Entity\License", "choices"})
     * @Assert\NotBlank()
     */
    public $license;
    /**
     * @var string[]
     * @Assert\Count(min=1)
     * @Assert\NotBlank()
     */
    public $languages;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromFont(Font $font): self
    {
        $command = new self($font->getId()->getValue());
        $command->slug = $font->getSlug();
        $command->name = $font->getName();
        $command->author = $font->getAuthor();
        $command->license = $font->getLicense()->getName();
        $command->languages = array_map(static function (Language $language): string {
            return $language->getName();
        }, $font->getLanguages());
        return $command;
    }
}
