<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\User\Entity\User;
use App\Tests\Functional\DbWebTestCase;
use Doctrine\ORM\EntityManager;

class SignupTest extends DbWebTestCase
{
    public function testRequest(): void
    {
        $this->client->request('GET', '/signup');

        $this->client->submitForm('Create Account', [
            'form[firstName]' => $firstName = 'New',
            'form[lastName]' => $lastName = 'User',
            'form[email]' => $email = 'new-user@app.loc',
            'form[password]' => 'password',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Check your email and follow the confirmation link.',
            $crawler->filter('.alert.alert-success')->text()
        );

        /** @var $em EntityManager */
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository(User::class);
        /** @var $signedUp User */
        $signedUp = $repo->findOneBy(['email'=>$email]);

        $this->assertSame($signedUp->getName()->getFirst(), $firstName);
        $this->assertSame($signedUp->getName()->getLast(), $lastName);
        $this->assertNotNull($signedUp->getConfirmToken());
    }

    public function testShortPassword(): void
    {
        $this->client->request('GET', '/signup');

        $crawler = $this->client->submitForm('Create Account', [
            'form[firstName]' => $firstName = 'New',
            'form[lastName]' => $lastName = 'User',
            'form[email]' => $email = 'new-user@app.loc',
            'form[password]' => 'short',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            'This value is too short. It should have 6 characters or more.',
            $crawler->filter('#form_password')->parents()->first()->filter('.form-error-message')->text()
        );
    }

    public function testNotEmail(): void
    {
        $this->client->request('GET', '/signup');

        $crawler = $this->client->submitForm('Create Account', [
            'form[firstName]' => 'New',
            'form[lastName]' => 'User',
            'form[email]' => 'not@email',
            'form[password]' => 'password',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            'This value is not a valid email address.',
            $crawler->filter('#form_email')->parents()->first()->filter('.form-error-message')->text()
        );
    }

    public function testExisting(): void
    {
        $this->client->request('GET', '/signup');

        $crawler = $this->client->submitForm('Create Account', [
            'form[firstName]' => 'New',
            'form[lastName]' => 'User',
            'form[email]' => AuthFixture::CONFIRMED_USER_EMAIL,
            'form[password]' => 'password',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            'User with this email already exists.',
            $crawler->filter('.alert.alert-danger')->text()
        );
    }

    public function testConfirm(): void
    {
        $this->client->request('GET', '/signup/'.AuthFixture::WAIT_USER_CONFIRM_TOKEN);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/', $this->client->getResponse()->headers->get('Location'));

        /** @var $em EntityManager */
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository(User::class);
        /** @var $signedUp User */
        $signedUp = $repo->findOneBy(['email'=>AuthFixture::WAIT_USER_EMAIL]);

        $this->assertNull($signedUp->getConfirmToken());
    }
}
