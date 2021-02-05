<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Create;

use App\Model\Flusher;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\Font;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Language;
use App\Model\Font\Entity\License;

class Handler
{
    private $fonts;
    private $flusher;

    public function __construct(FontRepository $fonts, Flusher $flusher)
    {
        $this->fonts = $fonts;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $font = new Font(
            Id::next(),
            new \DateTimeImmutable(),
            $command->slug,
            $command->name,
            $command->author,
            new License($command->license),
            array_map(function (string $name) {
                return new Language($name);
            }, array_unique($command->languages))
        );

        $this->fonts->add($font);

        $this->flusher->flush();
    }
}
