<?php

namespace Functional\Controller;

use App\DataFixtures\AccauntFixtures;
use App\DataFixtures\StrategyFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase
{
    public const TRADE_URL = '/trades';
    public const TRADE_STRATEGIES_URL = '/trades/strategies';
    public const TRADE_STRATEGY_ACCAUNT_URL = '/trades/strategy/1/accaunt/1';
    public const NOT_EXISTS_STRATEGY_TRADE_STRATEGY_ACCAUNT_URL = '/trades/strategy/10/accaunt/1';
    public const NOT_EXISTS_ACCAUNT_TRADE_STRATEGY_ACCAUNT_URL = '/trades/strategy/1/accaunt/10';

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

    public function testCanLoadListByStrategyAndAccaunt(): void
    {
        $client = static::createClient();
        $client->request('GET', self::TRADE_STRATEGY_ACCAUNT_URL);

        $title = StrategyFixtures::MY_STRATEGY_TITLE . '. ' . AccauntFixtures::ACCAUNT_ONE_TITLE;

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $title);
        $this->assertSelectorExists('table');
        $this->assertSelectorExists('canvas');
    }

    public function testNotFoundListByStrategyAndAccauntByStrategy(): void
    {
        $client = static::createClient();
        $client->request('GET', self::NOT_EXISTS_STRATEGY_TRADE_STRATEGY_ACCAUNT_URL);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testNotFoundListByStrategyAndAccauntByAccaount(): void
    {
        $client = static::createClient();
        $client->request('GET', self::NOT_EXISTS_ACCAUNT_TRADE_STRATEGY_ACCAUNT_URL);

        $this->assertResponseStatusCodeSame(404);
    }
}
