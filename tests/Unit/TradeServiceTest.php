<?php

namespace Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Entity\Trade;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasOpenStatusException;
use App\Exception\TradeHasUnknownTypeException;
use App\Service\TradeService;
use PHPUnit\Framework\TestCase;

class TradeServiceTest extends TestCase
{
//    private GraphService $graphService;
//
//    public function setUp(): void
//    {
//        $this->graphService = new TradeService();
//    }
//
//    public function tearDown(): void
//    {
//        unset($this->graphService);
//    }

    /**
     * @covers       \App\Service\TradeService::calculateTradeResult
     * @dataProvider provider
     */
    public function testCalculateTradeResult(Trade $trade, float $expected, string $message)
    {
        $this->assertSame(
            $expected,
            TradeService::calculateTradeResult($trade),
            $message
        );
    }

    /**
     * @covers       \App\Service\TradeService::calculateTradeResult
     */
    public function testCalculateTradeResultTradeHasOpenStatusException()
    {
        $this->expectException(TradeHasOpenStatusException::class);

        $trade = $this->getLongTrade();
        $trade->setStatus(Trade::STATUS_OPEN);

        TradeService::calculateTradeResult($trade);
    }

    /**
     * @covers       \App\Service\TradeService::calculateTradeResult
     */
    public function testCalculateTradeResultTradeNotHasClosePriceException()
    {
        $this->expectException(TradeHasNotClosePriceException::class);

        $trade = $this->getLongTrade();
        $trade->setClosePrice(null);

        TradeService::calculateTradeResult($trade);
    }

    /**
     * @covers       \App\Service\TradeService::calculateTradeResult
     */
    public function testCalculateTradeResultTradeHasUnknownTypeException()
    {
        $this->expectException(TradeHasUnknownTypeException::class);

        $trade = $this->getLongTrade();
        $trade->setType('Hello');

        TradeService::calculateTradeResult($trade);
    }

    /**
     * @todo Тесты на все остальное
     * но, нужно и мокать зависимости
     */

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
        return [
            [$this->getLongTrade(), 500.0, 'Сделка на long'],
            [$this->getShortTrade(), -200.0, 'Сделка на short'],
        ];
    }
}
