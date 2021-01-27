<?php

declare(strict_types=1);

namespace App\EventListeners;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;

class MailerSubscriber implements EventSubscriberInterface
{
    private $fromEmail;
    private $fromName;

    public function __construct(string $fromEmail, string $fromName)
    {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (!$email instanceof TemplatedEmail) {
            return;
        }
        $email->from(new Address($this->fromEmail, $this->fromName));
    }
}