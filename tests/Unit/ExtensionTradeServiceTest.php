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
     * @covers       \App\Service\TradeService::calculateResult
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

    /**
     * @throws \Exception
     */
    private function getLongTrade(string $status): Trade
    {
        $trade = TradeFixture::getLongTrade($status);
        $trade->setStock(StockFixture::getSber());
        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        $trade->setStrategy(StrategyFixture::getMyStrategy());

        return $trade;
    }

    /**
     * @throws \Exception
     */
    private function getShortTrade(string $status): Trade
    {
        $trade = TradeFixture::getShortTrade($status);
        $trade->setStock(StockFixture::getSber());
        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        $trade->setStrategy(StrategyFixture::getMyStrategy());

        return $trade;
    }

    /**
     * @throws \Exception
     */
    private function provider(): array
    {
        $longCloseTrade = $this->getLongTrade(Trade::STATUS_CLOSE);
        $shortCloseTrade = $this->getShortTrade(Trade::STATUS_CLOSE);
        $longOpenTrade = $this->getLongTrade(Trade::STATUS_OPEN);
        $shortOpenTrade = $this->getShortTrade(Trade::STATUS_OPEN);

        return [
            [
                [$longCloseTrade],
                [new ExtensionTrade($longCloseTrade, 500.0)],
                'Закрытая сделка на long c положительным результатом',
            ],
            [
                [$shortCloseTrade],
                [new ExtensionTrade($shortCloseTrade, -200.0)],
                'Закрытая cделка на short c отрицательным результатом',
            ],
            [
                [$longOpenTrade],
                [new ExtensionTrade($longOpenTrade, 845.0)],
                'Открытая сделка на long c положительным результатом',
            ],
            [
                [$shortOpenTrade],
                [new ExtensionTrade($shortOpenTrade, -845.0)],
                'Открытая cделка на short c отрицательным результатом',
            ],
        ];
    }
}
