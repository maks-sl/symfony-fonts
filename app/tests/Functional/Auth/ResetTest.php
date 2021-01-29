<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\User\Entity\User;
use App\Tests\Functional\DbWebTestCase;
use Doctrine\ORM\EntityManager;

class ResetTest extends DbWebTestCase
{
    public function testProcess(): void
    {
        // Request to password resetting

        $this->client->request('GET', '/reset');

        $this->client->submitForm('Reset Password', [
            'form[email]' => $email = AuthFixture::CONFIRMED_USER_EMAIL,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Password reset link was sent. Check your email.',
            $crawler->filter('.alert.alert-success')->text()
        );

        // Fetching reset token

        /** @var $em EntityManager */
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository(User::class);
        $token = $repo->findOneBy(['email'=>$email])->getResetToken()->getToken();

        // Trying to change a password

        $this->client->request('GET', '/reset/'.$token);

        $this->client->submitForm('Set Password', [
            'form[password]' => $newPassword = 'new_secret',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Password was successfully changed. You can use it to log in.',
            $crawler->filter('.alert.alert-success')->text()
        );;

        // Trying to login with a new password

        $this->client->submitForm('Login', [
            'email' => $email,
            'password' => $newPassword,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/', $this->client->getResponse()->headers->get('Location'));
    }

    public function testNotFound(): void
    {
        $this->client->request('GET', '/reset');

        $crawler = $this->client->submitForm('Reset Password', [
            'form[email]' => 'not-exist@e.mail',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'User is not found.',
            $crawler->filter('.alert.alert-danger')->text()
        );
    }
}
