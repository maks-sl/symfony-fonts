<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Sort;

use App\Model\Flusher;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Id;

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
        $font = $this->fonts->get(new Id($command->id));

        $font->sortFaces($command->faces);

        $this->flusher->flush();
    }
}

