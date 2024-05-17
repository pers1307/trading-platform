<?php

namespace App\Tests\Functional\Controller;

class TradeControllerTest extends BaseControllerTest
{
    public const TRADE_URL = '/trades';
    public const TRADES_ACTIVE_GROUP_STRATEGIES = '/trades/active/group/strategies';

    public function testCanLoadIndex(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::TRADE_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Позиции');
        $this->assertSelectorExists('table');

        $this->assertSelectorExists('.new-trade');
    }

    public function testCanLoadListActiveGroupByStrategies(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::TRADES_ACTIVE_GROUP_STRATEGIES);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Активные позиции');
        $this->assertSelectorExists('table');
    }
}
