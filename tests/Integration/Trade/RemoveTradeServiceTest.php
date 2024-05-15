<?php

namespace App\Tests\Integration\Trade;

use App\DataFixture\TradeFixture;
use App\Exception\NotFoundTradeException;
use App\Repository\TradeRepository;
use App\Service\Trade\RemoveTradeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RemoveTradeServiceTest extends KernelTestCase
{
    public function testRemove(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $removeTradeService = $container->get(RemoveTradeService::class);
        $removeTradeService->removeById(1);

        $tradeRepository = $container->get(TradeRepository::class);
        $result = $tradeRepository->findAll();
        $countTradres = count(TradeFixture::getTrades()) + 3;

        $this->assertCount($countTradres - 1, $result);
    }

    public function testNotFoundTradeException(): void
    {
        $this->expectException(NotFoundTradeException::class);

        self::bootKernel();
        $container = static::getContainer();
        $removeTradeService = $container->get(RemoveTradeService::class);
        $removeTradeService->removeById(1000);
    }
}
