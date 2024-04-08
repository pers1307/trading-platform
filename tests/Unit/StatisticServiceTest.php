<?php

namespace App\Tests\Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Entity\Trade;
use App\Service\ExtensionTradeService;
use App\Service\StatisticService;
use App\Service\TradeService;
use PHPUnit\Framework\TestCase;

class StatisticServiceTest extends TestCase
{
    private StatisticService $statisticService;
    private ExtensionTradeService $extensionTradeService;

    public function setUp(): void
    {
        $this->statisticService = new StatisticService();
        $this->extensionTradeService = new ExtensionTradeService(new TradeService());
    }

    public function tearDown(): void
    {
        unset($this->statisticService);
        unset($this->extensionTradeService);
    }

    private function getInput(): array
    {
        $trades = [];
        foreach (TradeFixture::getTrades() as $trade) {
            $trade->setStock(StockFixture::getGazp());
            $trade->setAccaunt(AccauntFixture::getTwoAccaunt());
            $trade->setStrategy(StrategyFixture::getMyStrategy());
            $trades[] = $trade;
        }

        $trades = array_filter($trades, static fn(Trade $trade) => Trade::STATUS_CLOSE === $trade->getStatus());

        return $this->extensionTradeService->convertTradesToExtensionTrades($trades);
    }

    /**
     * @covers       \App\Service\StatisticService::calculate
     */
    public function testCalculate()
    {
        $expected = $this->statisticService->calculate($this->getInput());

        // @todo Вот этого не должно быть, должна быть dto'шка
        $this->assertEquals(2, $expected->getCountLossTrades());
        $this->assertEquals(3, $expected->getCountProfitTrades());
        $this->assertEquals(300.0, $expected->getAverageProfit());
        $this->assertEquals(-80.0, $expected->getAverageLoss());
        $this->assertEquals(148, $expected->getExpectedValue());
    }
}
