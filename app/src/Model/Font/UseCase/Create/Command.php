<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
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
}
