<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase
{
    public const TRADE_URL = '/trades';
    public const TRADES_ACTIVE_GROUP_STRATEGIES = '/trades/active/group/strategies';

    public function testCanLoadIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', self::TRADE_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Позиции');
        $this->assertSelectorExists('table');

        $this->assertSelectorExists('.new-trade');
    }

    public function testCanLoadListActiveGroupByStrategies(): void
    {
        $client = static::createClient();
        $client->request('GET', self::TRADES_ACTIVE_GROUP_STRATEGIES);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Активные позиции');
        $this->assertSelectorExists('table');
    }
}
