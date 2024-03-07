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
    private TradeService $tradeService;

    public function setUp(): void
    {
        $this->tradeService = new TradeService();
    }

    public function tearDown(): void
    {
        unset($this->tradeService);
    }

    /**
     * @covers       \App\Service\TradeService::calculateResult
     * @dataProvider provider
     */
    public function testCalculateTradeResult(Trade $trade, float $expected, string $message)
    {
        $this->assertSame(
            $expected,
            $this->tradeService->calculateResult($trade),
            $message
        );
    }

    /**
     * @covers       \App\Service\TradeService::calculateResult
     */
    public function testCalculateTradeResultTradeHasOpenStatusException()
    {
        $this->expectException(TradeHasOpenStatusException::class);

        $trade = $this->getLongTrade();
        $trade->setStatus(Trade::STATUS_OPEN);

        $this->tradeService->calculateResult($trade);
    }

    /**
     * @covers       \App\Service\TradeService::calculateResult
     */
    public function testCalculateTradeResultTradeNotHasClosePriceException()
    {
        $this->expectException(TradeHasNotClosePriceException::class);

        $trade = $this->getLongTrade();
        $trade->setClosePrice(null);

        $this->tradeService->calculateResult($trade);
    }

    /**
     * @covers       \App\Service\TradeService::calculateResult
     */
    public function testCalculateTradeResultTradeHasUnknownTypeException()
    {
        $this->expectException(TradeHasUnknownTypeException::class);

        $trade = $this->getLongTrade();
        $trade->setType('Hello');

        $this->tradeService->calculateResult($trade);
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
        return [
            [$this->getLongTrade(), 500.0, 'Сделка на long'],
            [$this->getShortTrade(), -200.0, 'Сделка на short'],
        ];
    }
}
