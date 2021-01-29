<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;

class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/users');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());
        $this->client->request('GET', '/panel/users/create');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $crawler = $this->client->request('GET', '/panel/users/create');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Users', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', '/panel/users/create');

        $this->client->submitForm('Create', [
            'form[email]' => 'mark-twain@app.loc',
            'form[firstName]' => 'Mark',
            'form[lastName]' => 'Twain',
            'form[password]' => 'simple',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/panel/users', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Users', $crawler->filter('title')->text());
        $this->assertStringContainsString('Mark Twain', $crawler->filter('body')->text());
        $this->assertStringContainsString('mark-twain@app.loc', $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', '/panel/users/create');

        $crawler = $this->client->submitForm('Create', [
            'form[email]' => 'not-email',
            'form[firstName]' => '',
            'form[lastName]' => '',
            'form[password]' => '123',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('This value is not a valid email address.', $crawler
            ->filter('#form_email')->parents()->first()->filter('.form-error-message')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_firstName')->parents()->first()->filter('.form-error-message')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_lastName')->parents()->first()->filter('.form-error-message')->text());

        $this->assertStringContainsString('This value is too short. It should have 6 characters or more.', $crawler
            ->filter('#form_password')->parents()->first()->filter('.form-error-message')->text());
    }

    public function testExists(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', '/panel/users/create');

        $crawler = $this->client->submitForm('Create', [
            'form[firstName]' => 'Mark',
            'form[lastName]' => 'Twain',
            'form[email]' => UsersFixture::EXISTING_USER_EMAIL,
            'form[password]' => 'simple',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('User with this email already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}
