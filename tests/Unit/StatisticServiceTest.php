<?php

namespace Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Service\ExtensionTradeService;
use App\Service\GraphService;
use App\Service\StatisticService;
use App\Service\TradeService;
use PHPUnit\Framework\TestCase;

class StatisticServiceTest extends TestCase
{
    private StatisticService $statisticService;
    private ExtensionTradeService $extensionTradeService;

    public function setUp(): void
    {
        $this->statisticService = new StatisticService(new GraphService());
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

        return $this->extensionTradeService->convertTradesToExtensionTrades($trades);
    }

    /**
     * @covers       \App\Service\StatisticService::calculate
     */
    public function testCalculate()
    {
        $expected = $this->statisticService->calculate($this->getInput());

        $this->assertEquals(2, $expected->getCountLossTrades());
        $this->assertEquals(3, $expected->getCountProfitTrades());
        $this->assertEquals(300.0, $expected->getAverageProfit());
        $this->assertEquals(-80.0, $expected->getAverageLoss());
        $this->assertEquals(148, $expected->getExpectedValue());
        $this->assertEquals(
            '{"labels":["2024-03-01","2024-03-02","2024-03-03","2024-03-04","2024-03-05"],"values":[500,400,900,600,1100]}',
            $expected->getGraphFormatData()
        );
    }
}
