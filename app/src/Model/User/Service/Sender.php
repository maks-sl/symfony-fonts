<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\Email as UserEmail;
use App\Model\User\Entity\ResetToken;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Sender
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendResetToken(UserEmail $email, ResetToken $token): void
    {
        $message = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context([
                'token' => $token->getToken()
            ]);
        $this->mailer->send($message);
    }

    public function sendSignUpConfirmToken(UserEmail $email, string $token): void
    {
        $message = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Sig Up Confirmation')
            ->htmlTemplate('mail/user/signup.html.twig')
            ->context([
                'token' => $token
            ]);
        $this->mailer->send($message);
    }
}
