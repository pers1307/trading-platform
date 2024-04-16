<?php

namespace App\Tests\Unit;

use App\DataFixture\FixtureFabric;
use App\DataFixture\RiskProfileFixture;
use App\DataFixture\StockFixture;
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
     * @covers       \App\Service\CalculateService::calculateLotsByDeposit
     * @dataProvider providerCalculateLotsByDeposit
     * @throws Exception
     */
    public function testCalculateLotsByDeposit(RiskProfile $riskProfile, Stock|Trade $source, int $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculateLotsByDeposit($riskProfile, $source),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculateLotsByDeposit(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $fazp = StockFixture::getGazp();
        $sber = StockFixture::getSber();
        $longOpenTrade = FixtureFabric::getLongTrade(Trade::STATUS_OPEN, FixtureFabric::SBER);
        $shortOpenTrade = FixtureFabric::getShortTrade(Trade::STATUS_OPEN, FixtureFabric::SBER);

        $shortOpenTrade->setStock($fazp);
        $shortOpenTrade->setOpenPrice(250);

        return [
            [
                $riskProfile,
                $fazp,
                63,
                '10% от депозита 1 000 000 по акции GAZP это 63 лота',
            ],
            [
                $riskProfile,
                $sber,
                35,
                '10% от депозита 1 000 000 по акции SBER это 35 лота',
            ],
            //
            [
                $riskProfile,
                $longOpenTrade,
                50,
                'При цене SBER 200. При риске 10% от депозита 1 000 000 это 50 лотов',
            ],
            [
                $riskProfile,
                $shortOpenTrade,
                40,
                'При цене GAZP 200. При риске 10% от депозита 1 000 000 это 40 лотов',
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
            $this->calculateService->calculateLotsByTrade($riskProfile, $trade),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculateLotsByTradePersent(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $longOpenTrade = FixtureFabric::getLongTrade(Trade::STATUS_OPEN, FixtureFabric::SBER);
        $shortOpenTrade = FixtureFabric::getShortTrade(Trade::STATUS_OPEN, FixtureFabric::SBER);

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
     * @covers       \App\Service\CalculateService::calculatePersentByDeposit
     * @dataProvider providerCalculatePersentByDepositPersent
     * @throws Exception
     */
    public function testCalculateByDepositPersent(RiskProfile $riskProfile, Stock $stock, int $lots, float $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculatePersentByDeposit($riskProfile, $stock, $lots),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculatePersentByDepositPersent(): array
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
                '1 лот GAZP от депозита 1 000 000 это 0.158%',
            ],
            [
                $riskProfile,
                $sber,
                1,
                0.28,
                '1 лот SBER от депозита 1 000 000 это 0.28%',
            ],
        ];
    }

    /**
     * @covers       \App\Service\CalculateService::calculatePersentByTrade
     * @dataProvider providerCalculatePersentByTrade
     * @throws Exception
     */
    public function testCalculateByTradePersent(RiskProfile $riskProfile, Trade $trade, float $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->calculateService->calculatePersentByTrade($riskProfile, $trade),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function providerCalculatePersentByTrade(): array
    {
        $riskProfile = RiskProfileFixture::getRiskProfile(RiskProfile::TYPE_DEPOSIT);

        $longOpenTrade = FixtureFabric::getLongTrade(Trade::STATUS_OPEN, FixtureFabric::SBER);
        $shortOpenTrade = FixtureFabric::getShortTrade(Trade::STATUS_OPEN, FixtureFabric::SBER);

        return [
            [
                $riskProfile,
                $longOpenTrade,
                0.05,
                '50 пунктов SBER при 1 лоте от депозита 1 000 000 это 0.05%',
            ],
            [
                $riskProfile,
                $shortOpenTrade,
                0.02,
                '20 пунктов SBER при 1 лоте от депозита 1 000 000 это 0.02%',
            ],
        ];
    }
}
