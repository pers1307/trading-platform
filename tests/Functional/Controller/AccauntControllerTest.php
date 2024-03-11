<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccauntControllerTest extends WebTestCase
{
    public function testCanLoadIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/accaunts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Счета');
        $this->assertSelectorExists('table');
    }
}
