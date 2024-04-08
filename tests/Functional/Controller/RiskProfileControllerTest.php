<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RiskProfileControllerTest extends WebTestCase
{
    public function testCanLoadIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/risk-profiles');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Риск профили');
        $this->assertSelectorExists('table');
    }
}
