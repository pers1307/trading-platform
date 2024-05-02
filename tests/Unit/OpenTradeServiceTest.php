<?php

namespace App\Tests\Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Dto\Notification;
use App\Dto\OpenTradeNotifications;
use App\Entity\Trade;
use App\Entity\TradeCloseWarning;
use App\Service\OpenTradeService;
use PHPUnit\Framework\TestCase;

class OpenTradeServiceTest extends TestCase
{
    private OpenTradeService $openTradeService;

    public function setUp(): void
    {
        $this->openTradeService = new OpenTradeService();
    }

    public function tearDown(): void
    {
        unset($this->openTradeService);
    }

    /**
     * @param Trade[] $activeTrades
     * @covers       \App\Service\OpenTradeService::check
     * @dataProvider provider
     */
    public function testCheck(array $activeTrades, OpenTradeNotifications $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->openTradeService->check($activeTrades),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function getLongTrade(): Trade
    {
        $trade = TradeFixture::getLongTrade(Trade::STATUS_OPEN);
        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        $trade->setStrategy(StrategyFixture::getMyStrategy());

        return $trade;
    }

    /**
     * @throws \Exception
     */
    private function getShortTrade(): Trade
    {
        $trade = TradeFixture::getShortTrade(Trade::STATUS_OPEN);
        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        $trade->setStrategy(StrategyFixture::getMyStrategy());

        return $trade;
    }

    /**
     * @throws \Exception
     */
    private function provider(): array
    {
        $longCloseTrade = $this->getLongTrade();
        $shortCloseTrade = $this->getShortTrade();

        return [
            [
                [
                    (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(210.0)
                                ->setOpen(210.0)
                                ->setHigh(210.0)
                                ->setLow(210.0)
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Long. Цена не достигла значений take-profit или stop-loss',
            ],
            [
                [
                    (clone $longCloseTrade)
                        ->setStock(StockFixture::getSber())
                        ->setTakeProfit(null)
                        ->setStopLoss(null),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Long. Отсутствие take-profit и stop-loss',
            ],
            [
                [
                    $longCloseTradeTakeProfit = (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(255)
                                ->setOpen(250)
                                ->setHigh(255)
                                ->setLow(250)
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по take-profit",
                        "Счет №1. Моя стратегия. SBER. Long"
                    ),
                ], [$longCloseTradeTakeProfit]),
                'Сделка на Long. Цена выше значений take-profit',
            ],
            [
                [
                    $longCloseTradeTakeProfitHigh = (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(230)
                                ->setHigh(255),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по take-profit",
                        "Счет №1. Моя стратегия. SBER. Long"
                    ),
                ], [$longCloseTradeTakeProfitHigh]),
                'Сделка на Long. Цена ниже значений take-profit, но high дня его пересек',
            ],
            [
                [
                    $longCloseTradeStopLoss = (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(120)
                                ->setLow(120),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по stop-loss",
                        "Счет №1. Моя стратегия. SBER. Long"
                    ),
                ], [$longCloseTradeStopLoss]),
                'Сделка на Long. Цена ниже значений stop-loss',
            ],
            [
                [
                    $longCloseTradeLow = (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(180)
                                ->setLow(120),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по stop-loss",
                        "Счет №1. Моя стратегия. SBER. Long"
                    ),
                ], [$longCloseTradeLow]),
                'Сделка на Long. Цена выше значений stop-loss, но low дня его пересек',
            ],
            //
            [
                [
                    (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(210),
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Short. Цена не достигла значений take-profit или stop-loss',
            ],
            [
                [
                    (clone $shortCloseTrade)
                        ->setStock(StockFixture::getGazp())
                        ->setStopLoss(null)
                        ->setTakeProfit(null),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Short. Отсутствие take-profit и stop-loss',
            ],
            [
                [
                    $shortCloseTradeTakeProfit = (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(140)
                                ->setLow(140),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по take-profit",
                        "Счет №1. Моя стратегия. GAZP. Short"
                    ),
                ], [$shortCloseTradeTakeProfit]),
                'Сделка на Short. Цена ниже значений take-profit',
            ],
            [
                [
                    $shortCloseTradeTakeProfitLow = (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(160)
                                ->setLow(140),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по take-profit",
                        "Счет №1. Моя стратегия. GAZP. Short"
                    ),
                ], [$shortCloseTradeTakeProfitLow]),
                'Сделка на Short. Цена выше значений take-profit, но low дня его пересек',
            ],
            [
                [
                    $shortCloseTradeStopLoss = (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(230)
                                ->setHigh(230),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по stop-loss",
                        "Счет №1. Моя стратегия. GAZP. Short"
                    ),
                ], [$shortCloseTradeStopLoss]),
                'Сделка на Short. Цена выше значений stop-loss',
            ],
            [
                [
                    $shortCloseTradeStopLossHigh = (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(200)
                                ->setHigh(230),
                        ),
                ],
                new OpenTradeNotifications([
                    new Notification(
                        "Позиция закрылась по stop-loss",
                        "Счет №1. Моя стратегия. GAZP. Short"
                    ),
                ], [$shortCloseTradeStopLossHigh]),
                'Сделка на Short. Цена ниже значений stop-loss, но high дня его пересек',
            ],
            //
            [
                [],
                new OpenTradeNotifications([], []),
                'При отсутствии активных сделок не должно быть нотификаций',
            ],
            [
                [
                    (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(0)
                                ->setHigh(0)
                                ->setLow(0)
                                ->setOpen(0)
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Long. Не корректное обновление. Цена имеет значение 0',
            ],
            [
                [
                    (clone $longCloseTrade)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(null)
                                ->setHigh(null)
                                ->setLow(null)
                                ->setOpen(null)
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Long. Не корректное обновление. Цена имеет значение null',
            ],
            [
                [
                    (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(0)
                                ->setHigh(0)
                                ->setLow(0)
                                ->setOpen(0)
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Short. Не корректное обновление. Цена имеет значение 0',
            ],
            [
                [
                    (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(null)
                                ->setHigh(null)
                                ->setLow(null)
                                ->setOpen(null)
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Short. Не корректное обновление. Цена имеет значение null',
            ],
            //
            [
                [
                    (clone $longCloseTrade)
                        ->setStatus(Trade::STATUS_CLOSE)
                        ->setStock(
                            StockFixture::getSber()
                                ->setPrice(180)
                                ->setLow(120),
                        ),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Long. Нотификация не приходит, потому что сделка уже закрыта',
            ],
            [
                [
                    (clone $shortCloseTrade)
                        ->setStock(
                            StockFixture::getGazp()
                                ->setPrice(230)
                                ->setHigh(230),
                        )
                    ->setTradeCloseWarning(new TradeCloseWarning()),
                ],
                new OpenTradeNotifications([], []),
                'Сделка на Short. Цена выше значений stop-loss. Но, нотификации нет потому что она уже происходила',
            ],
        ];
    }
}
