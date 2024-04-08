<?php

namespace App\Tests\Integration;

use App\DataFixture\TradeFixture;
use App\Repository\RiskProfileRepository;
use App\Repository\TradeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RiskProfileServiceTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(RiskProfileRepository::class);
        $result = $tradeRepository->findAll();

        $this->assertCount(2, $result);
    }
}
