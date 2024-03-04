<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase
{
    public function testCanLoadIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trades');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Позиции');
        $this->assertSelectorExists('table');

        $this->assertSelectorExists('.new-trade');
        $this->assertSelectorExists('.edit-trade');
        $this->assertSelectorExists('.remove-trade');
    }
}
