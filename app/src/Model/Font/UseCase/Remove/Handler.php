<?php

declare(strict_types=1);

namespace App\Model\Font\UseCase\Remove;

use App\Model\EventsDispatcher;
use App\Model\Flusher;
use App\Model\Font\Entity\Event\FontRemoved;
use App\Model\Font\Entity\FontRepository;
use App\Model\Font\Entity\Id;

class Handler
{
    private $fonts;
    private $flusher;
    private $dispatcher;

    public function __construct(FontRepository $fonts, Flusher $flusher, EventsDispatcher $dispatcher)
    {
        $this->fonts = $fonts;
        $this->flusher = $flusher;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Command $command): void
    {
        $font = $this->fonts->get(new Id($command->id));

        $this->fonts->remove($font);

        $this->flusher->flush();

        $this->dispatcher->dispatch([
            new FontRemoved($font->getId()),
        ]);
    }
}