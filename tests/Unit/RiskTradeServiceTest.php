<?php

namespace App\Tests\Unit;

use App\DataFixture\FixtureFabric;
use App\Dto\ActiveTradesWithRisks;
use App\Dto\Notification;
use App\Dto\RiskTrades;
use App\Entity\RiskProfile;
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
     * @throws \Exception
     */
    public function testCheckExistsRiskWarning()
    {
        $longCloseTrade = FixtureFabric::getLongTrade(Trade::STATUS_CLOSE, FixtureFabric::SBER)
            ->setLots(1000)
            ->setTradeRiskWarning(new TradeRiskWarning());
        $depositeRiskProfile = FixtureFabric::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

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
    private function provider(): array
    {
        $longCloseTrade = FixtureFabric::getLongTrade(Trade::STATUS_CLOSE, FixtureFabric::SBER);
        $shortCloseTrade = FixtureFabric::getShortTrade(Trade::STATUS_CLOSE, FixtureFabric::SBER);
        $depositeRiskProfile = FixtureFabric::getRiskProfile(RiskProfile::TYPE_DEPOSIT);
        $tradeRiskProfile = FixtureFabric::getRiskProfile(RiskProfile::TYPE_TRADE);

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
                            "Счет №1. Моя стратегия. SBER. Long.\nПри риске 10% от депозита 1 000 000.\nРассчет: 50 лотов. Факт: 1000"
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
                            "Счет №1. Моя стратегия. SBER. Short.\nПри риске 15% от депозита 1 000 000.\nРассчет: 75 лотов. Факт: 1000"
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
                            "Счет №1. Моя стратегия. SBER. Long.\nПри риске 10% на сделку от депозита 1 000 000.\nРассчет: 200 лотов. Факт: 1000"
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
                            "Счет №1. Моя стратегия. SBER. Short.\nПри риске 15% на сделку от депозита 1 000 000.\nРассчет: 750 лотов. Факт: 1000"
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
