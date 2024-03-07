<?php

namespace Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Dto\ExtensionTrade;
use App\Entity\Trade;
use App\Service\ExtensionTradeService;
use App\Service\TradeService;
use PHPUnit\Framework\TestCase;

class ExtensionTradeServiceTest extends TestCase
{
    private ExtensionTradeService $extensionTradeService;

    public function setUp(): void
    {
        $this->extensionTradeService = new ExtensionTradeService(new TradeService());
    }

    public function tearDown(): void
    {
        unset($this->extensionTradeService);
    }

    /**
     * @covers       \App\Service\ExtensionTradeService::convertTradesToExtensionTrades
     * @dataProvider provider
     */
    public function testConvertTradesToExtensionTrades(array $trades, array $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->extensionTradeService->convertTradesToExtensionTrades($trades),
            $message
        );
    }

    private function getLongTrade(): Trade
    {
        $trade = TradeFixture::getLongTrade();
        $trade->setStock(StockFixture::getSber());
        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        $trade->setStrategy(StrategyFixture::getMyStrategy());

        return $trade;
    }

    private function getShortTrade(): Trade
    {
        $trade = TradeFixture::getShortTrade();
        $trade->setStock(StockFixture::getSber());
        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        $trade->setStrategy(StrategyFixture::getMyStrategy());

        return $trade;
    }

    private function provider(): array
    {
        $longTrade = $this->getLongTrade();
        $shortTrade = $this->getShortTrade();

        return [
            [
                [$longTrade],
                [new ExtensionTrade($longTrade, 500.0, 500.0)],
                'Сделка на long c положительным результатом',
            ],
            [
                [$shortTrade],
                [new ExtensionTrade($shortTrade, -200.0, -200.0)],
                'Сделка на short c отрицательным результатом',
            ],
        ];
    }
}
