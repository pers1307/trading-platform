<?php

namespace Unit;

use App\DataFixture\AccauntFixture;
use App\DataFixture\RiskProfileFixture;
use App\DataFixture\StockFixture;
use App\DataFixture\StrategyFixture;
use App\DataFixture\TradeFixture;
use App\Entity\RiskProfile;
use App\Entity\Stock;
use App\Entity\Trade;
use App\Service\CalculateService;
use Exception;
use PHPUnit\Framework\TestCase;

class CalculateServiceTest extends TestCase
{
    private CalculateService $calculateService;

    public function setUp(): void
    {
        $this->calculateService = new CalculateService();
    }

    public function tearDown(): void
    {
        unset($this->calculateService);
    }

    /**
     * @covers       \App\Service\CalculateService::calculateByDepositPersent
     * @dataProvider providerCalculateByDepositPersent
     * @throws Exception
     */
    public function testCalculateByDepositPersent(RiskProfile $riskProfile, Stock $stock, int $lots, float $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculateByDepositPersent($riskProfile, $stock, $lots),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculateByDepositPersent(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $fazp = StockFixture::getGazp();
        $sber = StockFixture::getSber();

        return [
            [
                $riskProfile,
                $fazp,
                1,
                0.16,
                '1 лот GAZP от депозита 1 000 000 это 0.158 %',
            ],
            [
                $riskProfile,
                $sber,
                1,
                0.28,
                '1 лот SBER от депозита 1 000 000 это 0.28 %',
            ],
        ];
    }

    /**
     * @covers       \App\Service\CalculateService::calculateLotsByDepositPersent
     * @dataProvider providerCalculateLotsByDepositPersent
     * @throws Exception
     */
    public function testCalculateLotsByDepositPersent(RiskProfile $riskProfile, Stock $stock, int $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculateLotsByDepositPersent($riskProfile, $stock),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculateLotsByDepositPersent(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $fazp = StockFixture::getGazp();
        $sber = StockFixture::getSber();

        return [
            [
                $riskProfile,
                $fazp,
                63,
                '10% от депозита 1 000 000 по акции GAZP это 64 лота',
            ],
            [
                $riskProfile,
                $sber,
                35,
                '10% от депозита 1 000 000 по акции SBER это 35 лота',
            ],
        ];
    }

    /**
     * @covers       \App\Service\CalculateService::calculateByTradePersent
     * @dataProvider providerCalculateByTradePersent
     * @throws Exception
     */
    public function testCalculateByTradePersent(RiskProfile $riskProfile, Trade $trade, float $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculateByTradePersent($riskProfile, $trade),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculateByTradePersent(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $longOpenTrade = $this->getLongTrade(Trade::STATUS_OPEN);
        $shortOpenTrade = $this->getShortTrade(Trade::STATUS_OPEN);

        return [
            [
                $riskProfile,
                $longOpenTrade,
                0.05,
                '50 пунктов SBER при 1 лоте от депозита 1 000 000 это 0.05 %',
            ],
            [
                $riskProfile,
                $shortOpenTrade,
                0.02,
                '20 пунктов SBER при 1 лоте от депозита 1 000 000 это 0.02 %',
            ],
        ];
    }

    /**
     * @covers       \App\Service\CalculateService::calculateLotsByTradePersent
     * @dataProvider providerCalculateLotsByTradePersent
     * @throws Exception
     */
    public function testCalculateLotsByTradePersent(RiskProfile $riskProfile, Trade $trade, int $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculateLotsByTradePersent($riskProfile, $trade),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculateLotsByTradePersent(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $longOpenTrade = $this->getLongTrade(Trade::STATUS_OPEN);
        $shortOpenTrade = $this->getShortTrade(Trade::STATUS_OPEN);

        return [
            [
                $riskProfile,
                $longOpenTrade,
                200,
                '50 пунктов SBER от депозита 1 000 000 при риске 10% это 12 лотов',
            ],
            [
                $riskProfile,
                $shortOpenTrade,
                500,
                '20 пунктов SBER от депозита 1 000 000 при риске 10% это 10 лотов',
            ],
        ];
    }

    /**
     * @throws \Exception
     * @todo можно вынести эти методы в абстрактный класс и не заниматься их дублированием
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
