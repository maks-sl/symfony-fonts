<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Hide;

use App\Model\Flusher;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\Status;

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

        $font->changeStatus(Status::hidden());

        $this->flusher->flush();
    }
}