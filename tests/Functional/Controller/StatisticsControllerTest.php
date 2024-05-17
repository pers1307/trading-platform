<?php

namespace App\Tests\Functional\Controller;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StrategyFixture;

class StatisticsControllerTest extends BaseControllerTest
{
    public const STATISTICS_STRATEGIES_URL = '/statistics/strategies';
    public const STATISTICS_STRATEGY_ACCAUNT_URL = '/statistics/strategy/1/accaunt/1';
    public const NOT_EXISTS_STRATEGY_STATISTICS_STRATEGY_ACCAUNT_URL = '/statistics/strategy/10/accaunt/1';
    public const NOT_EXISTS_ACCAUNT_STATISTICS_STRATEGY_ACCAUNT_URL = '/statistics/strategy/1/accaunt/10';

    public function testCanLoadListByStrategies(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::STATISTICS_STRATEGIES_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Статистика по стратегиям');
        $this->assertSelectorExists('table');
    }

    public function testCanLoadListByStrategyAndAccaunt(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::STATISTICS_STRATEGY_ACCAUNT_URL);

        $title = StrategyFixture::MY_STRATEGY_TITLE . '. ' . AccauntFixture::ACCAUNT_ONE_TITLE;

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $title);
        $this->assertSelectorExists('table');
        $this->assertSelectorExists('canvas');
    }

    public function testNotFoundListByStrategyAndAccauntByStrategy(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::NOT_EXISTS_STRATEGY_STATISTICS_STRATEGY_ACCAUNT_URL);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testNotFoundListByStrategyAndAccauntByAccaount(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::NOT_EXISTS_ACCAUNT_STATISTICS_STRATEGY_ACCAUNT_URL);

        $this->assertResponseStatusCodeSame(404);
    }
}
