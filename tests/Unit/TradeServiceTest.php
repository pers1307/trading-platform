<?php

namespace Unit;

use App\DataFixtures\AccauntFixtures;
use App\DataFixtures\StrategyFixtures;
use App\Entity\Accaunt;
use App\Entity\Stock;
use App\Entity\Strategy;
use App\Entity\Trade;
use App\Exception\TradeHasOpenStatusException;
use App\Exception\TradeHasUnknownTypeException;
use App\Exception\TradeHasNotClosePriceException;
use App\Service\TradeService;
use PHPUnit\Framework\TestCase;

class TradeServiceTest extends TestCase
{
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

    private function getStock(): Stock
    {
        return (new Stock())
            ->setTitle('Сбербанк России ПАО ао')
            ->setSecId('SBER')
            ->setPrice(284.77)
            ->setLotSize(10)
            ->setMinStep(0.01);
    }

    private function getAccaunt(): Accaunt
    {
        return (new Accaunt())
            ->setTitle(AccauntFixtures::ACCAUNT_ONE_TITLE)
            ->setBrockerTitle('239900****CG');
    }

    private function getStrategy(): Strategy
    {
        return (new Strategy())
            ->setTitle(StrategyFixtures::MY_STRATEGY_TITLE)
            ->setDescription('')
            ->setStatus('active');
    }

    private function getLongTrade(): Trade
    {
        $trade = new Trade();
        $trade->setStock($this->getStock());
        $trade->setAccaunt($this->getAccaunt());
        $trade->setStrategy($this->getStrategy());
        $trade->setType(Trade::TYPE_LONG);
        $trade->setOpenDateTime(new \DateTime('2024-03-01 08:05:19'));
        $trade->setOpenPrice(200.0);
        $trade->setCloseDateTime(new \DateTime('2024-03-03 10:00:00'));
        $trade->setClosePrice(250.0);
        $trade->setStopLoss(150);
        $trade->setTakeProfit(250);
        $trade->setLots(1);
        $trade->setStatus(Trade::STATUS_CLOSE);
        $trade->setDescription('');

        return $trade;
    }

    private function getShortTrade(): Trade
    {
        $trade = new Trade();
        $trade->setStock($this->getStock());
        $trade->setAccaunt($this->getAccaunt());
        $trade->setStrategy($this->getStrategy());
        $trade->setType(Trade::TYPE_SHORT);
        $trade->setOpenDateTime(new \DateTime('2024-03-01 10:05:19'));
        $trade->setOpenPrice(200.00);
        $trade->setCloseDateTime(new \DateTime('2024-03-05 10:05:19'));
        $trade->setClosePrice(220.00);
        $trade->setStopLoss(220);
        $trade->setTakeProfit(150);
        $trade->setLots(1);
        $trade->setStatus(Trade::STATUS_CLOSE);
        $trade->setDescription('');

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
