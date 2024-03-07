<?php

namespace App\Service;

use App\Dto\ExtensionTradesCollection;
use App\Entity\Trade;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasOpenStatusException;
use App\Exception\TradeHasUnknownTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @todo: нет интеграционных тестов
 */
class ExtensionTradeCollectionService
{
    public function __construct(
        private bool $isDebug,
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheInterface $cache,
        private readonly StatisticService $statisticService,
        private readonly ExtensionTradeService $extensionTradeService,
    ) {
    }

    /**
     * @return ExtensionTradesCollection
     * @throws InvalidArgumentException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasOpenStatusException
     * @throws TradeHasUnknownTypeException
     */
    public function getCollection(
        int $strategyId,
        int $accauntId
    ): ExtensionTradesCollection {
        if ($this->isDebug) {
            $tradeRepository = $this->entityManager->getRepository(Trade::class);
            $trades = $tradeRepository->findAllCloseByStrategyIdAndAccauntId($strategyId, $accauntId);
            $extensionTrades = $this->extensionTradeService->convertTradesToExtensionTrades($trades);

            return $this->statisticService->calculate($extensionTrades);
        }

        return $this->cache->get('extension_trades_collection_' . $strategyId . '_' . $accauntId, function (CacheItemInterface $cacheItem) use ($strategyId, $accauntId) {
            $cacheItem->expiresAfter(60 * 60 * 24);

            $tradeRepository = $this->entityManager->getRepository(Trade::class);
            $trades = $tradeRepository->findAllCloseByStrategyIdAndAccauntId($strategyId, $accauntId);
            $extensionTrades = $this->extensionTradeService->convertTradesToExtensionTrades($trades);

            return $this->statisticService->calculate($extensionTrades);
        });
    }
}
