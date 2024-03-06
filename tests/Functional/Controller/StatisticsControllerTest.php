<?php

namespace Functional\Controller;

use App\DataFixtures\AccauntFixtures;
use App\DataFixtures\StrategyFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatisticsControllerTest extends WebTestCase
{
    public const STATISTICS_STRATEGIES_URL = '/statistics/strategies';
    public const STATISTICS_STRATEGY_ACCAUNT_URL = '/statistics/strategy/1/accaunt/1';
    public const NOT_EXISTS_STRATEGY_STATISTICS_STRATEGY_ACCAUNT_URL = '/statistics/strategy/10/accaunt/1';
    public const NOT_EXISTS_ACCAUNT_STATISTICS_STRATEGY_ACCAUNT_URL = '/statistics/strategy/1/accaunt/10';

    public function testCanLoadListByStrategies(): void
    {
        $client = static::createClient();
        $client->request('GET', self::STATISTICS_STRATEGIES_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Статистика по стратегиям');
        $this->assertSelectorExists('table');
    }
//
//    public function testCanLoadListByStrategyAndAccaunt(): void
//    {
//        $client = static::createClient();
//        $client->request('GET', self::STATISTICS_STRATEGY_ACCAUNT_URL);
//
//        $title = StrategyFixtures::MY_STRATEGY_TITLE . '. ' . AccauntFixtures::ACCAUNT_ONE_TITLE;
//
//        $this->assertResponseIsSuccessful();
//        $this->assertSelectorTextContains('h1', $title);
//        $this->assertSelectorExists('table');
//        $this->assertSelectorExists('canvas');
//    }
//
//    public function testNotFoundListByStrategyAndAccauntByStrategy(): void
//    {
//        $client = static::createClient();
//        $client->request('GET', self::NOT_EXISTS_STRATEGY_STATISTICS_STRATEGY_ACCAUNT_URL);
//
//        $this->assertResponseStatusCodeSame(404);
//    }
//
//    public function testNotFoundListByStrategyAndAccauntByAccaount(): void
//    {
//        $client = static::createClient();
//        $client->request('GET', self::NOT_EXISTS_ACCAUNT_STATISTICS_STRATEGY_ACCAUNT_URL);
//
//        $this->assertResponseStatusCodeSame(404);
//    }
}
