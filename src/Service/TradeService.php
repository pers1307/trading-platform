<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Dto\ExtensionTradesCollection;
use App\Entity\Trade;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasOpenStatusException;
use App\Exception\TradeHasUnknownTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class TradeService
{
    public function __construct(
        private bool $isDebug,
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheInterface $cache,
        private readonly StatisticService $statisticService,
    ) {
    }

    /**
     * @throws TradeHasOpenStatusException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownTypeException
     */
    public static function calculateTradeResult(Trade $trade): float
    {
        if (Trade::STATUS_OPEN === $trade->getStatus()) {
            throw new TradeHasOpenStatusException();
        }
        if (empty($trade->getClosePrice())) {
            throw new TradeHasNotClosePriceException();
        }

        if (Trade::TYPE_LONG === $trade->getType()) {
            return ($trade->getClosePrice() - $trade->getOpenPrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }
        if (Trade::TYPE_SHORT === $trade->getType()) {
            return ($trade->getOpenPrice() - $trade->getClosePrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }

        throw new TradeHasUnknownTypeException();
    }

    /**
     * @param Trade[] $trades
     * @return ExtensionTrade[]
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasOpenStatusException
     * @throws TradeHasUnknownTypeException
     */
    public function convertTradesToExtensionTrades(array $trades): array
    {
        $extensionTrades = [];
        $cumulativeTotal = 0.0;
        foreach ($trades as $trade) {
            $tradeResult = $this::calculateTradeResult($trade);
            $cumulativeTotal = round($cumulativeTotal + $tradeResult, 2);
            $extensionTrades[] = new ExtensionTrade($trade, $tradeResult, $cumulativeTotal);
        }

        return $extensionTrades;
    }

    /**
     * @return ExtensionTrade[]
     */
    public function getExtensionTrades(
        int $strategyId,
        int $accauntId
    ): array {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $trades = $tradeRepository->findAllCloseByStrategyIdAndAccauntId($strategyId, $accauntId);

        return $this->convertTradesToExtensionTrades($trades);
    }

    /**
     * @return ExtensionTradesCollection
     * @throws InvalidArgumentException
     */
    public function getExtensionTradesCollection(
        int $strategyId,
        int $accauntId
    ): ExtensionTradesCollection {
        if ($this->isDebug) {
            $extensionTrades = $this->getExtensionTrades($strategyId, $accauntId);
            return $this->statisticService->calculate($extensionTrades);
        }

        return $this->cache->get('extension_trades_collection_' . $strategyId . '_' . $accauntId, function (CacheItemInterface $cacheItem) use ($strategyId, $accauntId) {
            $cacheItem->expiresAfter(60 * 60 * 24);

            $extensionTrades = $this->getExtensionTrades($strategyId, $accauntId);
            return $this->statisticService->calculate($extensionTrades);
        });
    }
}
