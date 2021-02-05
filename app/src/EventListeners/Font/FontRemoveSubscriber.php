<?php

declare(strict_types=1);

namespace App\EventListeners\Font;

use App\Model\Font\Entity\Event\FontRemoved;
use App\Model\Font\Service\File\FileManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FontRemoveSubscriber implements EventSubscriberInterface
{
    private $manager;

    public function __construct(FileManager $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FontRemoved::class => 'onFontRemoved',
        ];
    }

    public function onFontRemoved(FontRemoved $event): void
    {
        $this->manager->removeDir($event->id->getValue());
    }
}