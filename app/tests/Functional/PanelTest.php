<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class PanelTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());
        $crawler = $this->client->request('GET', '/panel');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdmin(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $crawler = $this->client->request('GET', '/panel');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Panel', $crawler->filter('title')->text());
    }
}
