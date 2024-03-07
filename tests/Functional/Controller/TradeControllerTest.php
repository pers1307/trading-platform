<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase
{
    public const TRADE_URL = '/trades';
    public const TRADE_STRATEGIES_URL = '/trades/strategies';

    public function testCanLoadIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', self::TRADE_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Позиции');
        $this->assertSelectorExists('table');

        $this->assertSelectorExists('.new-trade');
    }

    public function testCanLoadListByStrategies(): void
    {
        $client = static::createClient();
        $client->request('GET', self::TRADE_STRATEGIES_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Позиции по стратегиям');
        $this->assertSelectorExists('table');
    }
}
