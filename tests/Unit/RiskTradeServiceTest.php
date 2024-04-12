<?php

namespace App\Tests\Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\RiskProfileFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Dto\ActiveTradesWithRisks;
use App\Dto\Notification;
use App\Dto\RiskTrades;
use App\Entity\Accaunt;
use App\Entity\RiskProfile;
use App\Entity\Strategy;
use App\Entity\Trade;
use App\Entity\TradeRiskWarning;
use App\Service\CalculateService;
use App\Service\RiskTradeService;
use PHPUnit\Framework\TestCase;

class RiskTradeServiceTest extends TestCase
{
    private RiskTradeService $riskTradeService;

    public function setUp(): void
    {
        $this->riskTradeService = new RiskTradeService(new CalculateService());
    }

    public function tearDown(): void
    {
        unset($this->riskTradeService);
    }

    /**
     * @covers       \App\Service\RiskTradeService::check
     * @dataProvider provider
     */
    public function testCheck(ActiveTradesWithRisks $activeTradesWithRisks, RiskTrades $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->riskTradeService->check($activeTradesWithRisks),
            $message
        );
    }

    /**
     * @covers       \App\Service\RiskTradeService::check
     */
    public function testCheckExistsRiskWarning()
    {
        $longCloseTrade = $this
            ->getLongTrade(Trade::STATUS_CLOSE)
            ->setLots(1000)
            ->setTradeRiskWarning(new TradeRiskWarning());
        $depositeRiskProfile = $this->getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $activeTradesWithRisks = new ActiveTradesWithRisks(
            [$longCloseTrade],
            [
                "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => $depositeRiskProfile,
            ]
        );
        $riskTrades = new RiskTrades([], []);

        $this->assertEquals(
            $riskTrades,
            $this->riskTradeService->check($activeTradesWithRisks),
            'Сделки с риск профилем 10% от депозита 1 000 000 руб.. Лонг сбера с большим количество лотов. Нотификация уже существует',
        );
    }

    /**
     * @throws \Exception
     */
    private function getLongTrade(string $status): Trade
    {
        $trade = TradeFixture::getLongTrade($status);
        $trade->setStock(StockFixture::getSber());
        $trade->setAccaunt($this->getAccaunt());
        $trade->setStrategy($this->getStrategy());

        return $trade;
    }

    /**
     * @throws \Exception
     */
    private function getShortTrade(string $status): Trade
    {
        $trade = TradeFixture::getShortTrade($status);
        $trade->setStock(StockFixture::getSber());
        $trade->setAccaunt($this->getAccaunt());
        $trade->setStrategy($this->getStrategy());

        return $trade;
    }

    private function getRiskProfile(string $type): RiskProfile
    {
        $riskProfile = RiskProfileFixture::getRiskProfile($type);
        $riskProfile->setAccaunt($this->getAccaunt());
        $riskProfile->setStrategy($this->getStrategy());

        return $riskProfile;
    }

    private function getAccaunt(): Accaunt
    {
        $accaunt = AccauntFixture::getOneAccaunt();

        $reflection = new \ReflectionClass($accaunt);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($accaunt, 1);

        return $accaunt;
    }

    private function getStrategy(): Strategy
    {
        $strategy = StrategyFixture::getMyStrategy();

        $reflection = new \ReflectionClass($strategy);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($strategy, 1);

        return $strategy;
    }

    /**
     * @throws \Exception
     */
    private function provider(): array
    {
        $longCloseTrade = $this->getLongTrade(Trade::STATUS_CLOSE);
        $shortCloseTrade = $this->getShortTrade(Trade::STATUS_CLOSE);
        $depositeRiskProfile = $this->getRiskProfile(RiskProfile::TYPE_DEPOSIT);
        $tradeRiskProfile = $this->getRiskProfile(RiskProfile::TYPE_TRADE);

        return [
            [
                new ActiveTradesWithRisks(
                    [clone $longCloseTrade->setLots(1000)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $depositeRiskProfile,
                    ]
                ),
                new RiskTrades(
                    [clone $longCloseTrade->setLots(1000)],
                    [
                        new Notification(
                            "Нарушение риск-менеджмента!",
                            "Счет №1. Моя стратегия. SBER. Long.\nРассчет: 50 лотов. Факт: 1000"
                        ),
                    ]
                ),
                'Сделки с риск профилем 10% от депозита 1 000 000 руб.. Лонг сбера с большим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $longCloseTrade->setLots(10)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $depositeRiskProfile,
                    ]
                ),
                new RiskTrades([], []),
                'Сделки с риск профилем 10% от депозита 1 000 000 руб.. Лонг сбера с меньшим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $shortCloseTrade->setLots(1000)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $depositeRiskProfile->setPersent(15),
                    ]
                ),
                new RiskTrades(
                    [clone $shortCloseTrade->setLots(1000)],
                    [
                        new Notification(
                            "Нарушение риск-менеджмента!",
                            "Счет №1. Моя стратегия. SBER. Short.\nРассчет: 75 лотов. Факт: 1000"
                        ),
                    ]
                ),
                'Сделки с риск профилем 15% от депозита 1 000 000 руб..Шорт сбера с большим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $shortCloseTrade->setLots(10)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $depositeRiskProfile,
                    ]
                ),
                new RiskTrades([], []),
                'Сделки с риск профилем 10% от депозита 1 000 000 руб..Шорт сбера с меньшим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $longCloseTrade->setLots(1000)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $tradeRiskProfile,
                    ]
                ),
                new RiskTrades(
                    [clone $longCloseTrade->setLots(1000)],
                    [
                        new Notification(
                            "Нарушение риск-менеджмента!",
                            "Счет №1. Моя стратегия. SBER. Long.\nРассчет: 200 лотов. Факт: 1000"
                        ),
                    ]
                ),
                'Сделки с риск профилем 10% от риска в сделке на депозит 1 000 000 руб.. Лонг сбера с большим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $longCloseTrade->setLots(10)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $tradeRiskProfile,
                    ]
                ),
                new RiskTrades([], []),
                'Сделки с риск профилем 10% от риска в сделке на депозит 1 000 000 руб.. Лонг сбера с меньшим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $shortCloseTrade->setLots(1000)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $tradeRiskProfile->setPersent(15),
                    ]
                ),
                new RiskTrades(
                    [clone $shortCloseTrade->setLots(1000)],
                    [
                        new Notification(
                            "Нарушение риск-менеджмента!",
                            "Счет №1. Моя стратегия. SBER. Short.\nРассчет: 750 лотов. Факт: 1000"
                        ),
                    ]
                ),
                'Сделки с риск профилем 15% от риска в сделке на депозит 1 000 000 руб.. Шорт сбера с большим количество лотов',
            ],
            [
                new ActiveTradesWithRisks(
                    [clone $shortCloseTrade->setLots(10)],
                    [
                        "{$depositeRiskProfile->getAccaunt()->getId()}-{$depositeRiskProfile->getStrategy()->getId()}" => clone $tradeRiskProfile,
                    ]
                ),
                new RiskTrades([], []),
                'Сделки с риск профилем 10% от риска в сделке на депозит 1 000 000 руб.. Шорт сбера с меньшим количество лотов',
            ],
        ];
    }
}
