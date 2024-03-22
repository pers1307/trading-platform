<?php

namespace Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;
use App\Service\TradeService;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Здесь тестируются только исключения
 * @see ExtensionTradeServiceTest
 */
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
     * @throws Exception
     */
    public function testCalculateTradeResultTradeNotHasClosePriceException()
    {
        $this->expectException(TradeHasNotClosePriceException::class);

        $trade = $this->getLongTrade(Trade::STATUS_CLOSE);
        $trade->setClosePrice(null);

        $this->tradeService->calculateResult($trade);
    }

    /**
     * @covers       \App\Service\TradeService::calculateResult
     * @throws Exception
     */
    public function testCalculateTradeResultStockHasNotPriceException()
    {
        $this->expectException(StockHasNotPriceException::class);

        $trade = $this->getLongTrade(Trade::STATUS_OPEN);
        $trade->getStock()->setPrice(null);

        $this->tradeService->calculateResult($trade);
    }

    /**
     * @covers       \App\Service\TradeService::calculateResult
     * @throws Exception
     */
    public function testCalculateTradeResultTradeHasUnknownStatusException()
    {
        $this->expectException(TradeHasUnknownStatusException::class);

        $trade = $this->getLongTrade(Trade::STATUS_OPEN);

        $reflection = new \ReflectionClass($trade);
        $property = $reflection->getProperty('status');
        $property->setValue($trade, 'unknown');

        $this->tradeService->calculateResult($trade);
    }

    /**
     * @todo: переименовать тесты по смыслу Delivery_with_a_past_date_is_invalid()
     * по поведенческому смыслу
     * @covers       \App\Service\TradeService::calculateResult
     * @throws Exception
     */
    public function testCalculateTradeResultTradeHasUnknownTypeException()
    {
        $this->expectException(TradeHasUnknownTypeException::class);

        $trade = $this->getLongTrade(Trade::STATUS_OPEN);

        $reflection = new \ReflectionClass($trade);
        $property = $reflection->getProperty('type');
        $property->setValue($trade, 'unknown');

        $this->tradeService->calculateResult($trade);
    }

    /**
     * @todo можно вынести эти методы в абстрактный класс и не заниматься их дублированием
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
}
