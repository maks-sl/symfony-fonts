<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class HomeTest extends DbWebTestCase
{
    public function testSuccess(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Welcome!', $crawler->filter('title')->text());
    }
}
