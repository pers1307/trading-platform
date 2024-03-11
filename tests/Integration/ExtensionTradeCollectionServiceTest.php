<?php

namespace Integration;

use App\Dto\ExtensionTradesCollection;
use App\Service\ExtensionTradeCollectionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExtensionTradeCollectionServiceTest extends KernelTestCase
{
    public function testGetCollection(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $extensionTradeCollectionService = $container->get(ExtensionTradeCollectionService::class);
        $actual = $extensionTradeCollectionService->getCollection(1, 1);

        $this->assertInstanceOf(ExtensionTradesCollection::class, $actual);
    }
}
