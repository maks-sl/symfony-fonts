<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\DbWebTestCase;

class LoginTest extends DbWebTestCase
{
    public function testSuccess(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Login', [
            'email' => 'auth-user@app.loc',
            'password' => 'password',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/', $this->client->getResponse()->headers->get('Location'));
    }

    public function testNotValid(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Login', [
            'email' => 'auth-user@app.loc',
            'password' => 'bad',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Invalid credentials.',
            $crawler->filter('.alert.alert-danger')->text()
        );
    }

    public function testWait(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Login', [
            'email' => AuthFixture::WAIT_USER_EMAIL,
            'password' => 'password',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Account is disabled.',
            $crawler->filter('.alert.alert-danger')->text()
        );
    }
}
