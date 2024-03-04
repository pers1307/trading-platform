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
    }

    public function testCanLoadListByStrategies(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trades/strategies');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Позиции по стратегиям');
        $this->assertSelectorExists('table');
    }
}
