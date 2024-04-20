<?php

namespace App\Tests\Integration;

use App\Dto\ExtensionTradesCollection;
use App\Service\ExtensionTradeCollectionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExtensionTradeCollectionServiceTest extends KernelTestCase
{
    public function testGetNotEmptyCollection(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $extensionTradeCollectionService = $container->get(ExtensionTradeCollectionService::class);
        $actual = $extensionTradeCollectionService->getCollection(1, 1);

        $this->assertInstanceOf(ExtensionTradesCollection::class, $actual);
        $this->assertNotEmpty($actual->getExtensionTrades());
    }

    public function testGetEmptyCollection(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $extensionTradeCollectionService = $container->get(ExtensionTradeCollectionService::class);
        $actual = $extensionTradeCollectionService->getCollection(2, 1);

        $this->assertInstanceOf(ExtensionTradesCollection::class, $actual);
        $this->assertEmpty($actual->getExtensionTrades());
        $this->assertNull($actual->getGraph());
        $this->assertNull($actual->getStatistic());
        $this->assertNull($actual->getCumulativeTotalArray());
    }
}
