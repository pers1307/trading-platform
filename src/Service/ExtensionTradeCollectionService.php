<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Dto\ExtensionTradesCollection;
use App\Dto\Graph;
use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @todo: нет интеграционных тестов
 */
class ExtensionTradeCollectionService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExtensionTradeService $extensionTradeService,
        private readonly StatisticService $statisticService,
        private readonly GraphService $graphService,
        private readonly CumulativeTotalService $cumulativeTotalService,
    ) {
    }

    /**
     * @throws StockHasNotPriceException
     * @throws TradeHasUnknownTypeException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownStatusException
     */
    public function getCollection(
        int $strategyId,
        int $accauntId
    ): ExtensionTradesCollection {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $trades = $tradeRepository->findAllCloseByStrategyIdAndAccauntId($strategyId, $accauntId);
        $extensionTrades = $this->extensionTradeService->convertTradesToExtensionTrades($trades);

        if (empty($extensionTrades)) {
            return new ExtensionTradesCollection(
                $extensionTrades,
                null,
                null,
                null
            );
        }

        /**
         * @todo: вернуть дто в котором есть нулевой элемент.
         * Для того, чтобы правильно считать статистику по торговле
         */

        $statistic = $this->statisticService->calculate($extensionTrades);
        $cumulativeTotalArray = $this->cumulativeTotalService->calculate($extensionTrades);
        $graph = $this->getGraph($extensionTrades, $cumulativeTotalArray);

        return new ExtensionTradesCollection(
            $extensionTrades,
            $statistic,
            $graph,
            $cumulativeTotalArray
        );
    }

    /**
     * @param ExtensionTrade[] $extensionTrades
     * @param array $cumulativeTotalArray
     */
    private function getGraph(array $extensionTrades, array $cumulativeTotalArray): Graph
    {
        $formatArray = array_map(
            static fn(ExtensionTrade $extensionTrade, $cumulativeTotal) => ['extensionTrade' => $extensionTrade, 'cumulativeTotal' => $cumulativeTotal],
            $extensionTrades,
            $cumulativeTotalArray
        );

        return $this->graphService->format(
            $formatArray,
            static fn(int $key, array $formatItem) => $formatItem['extensionTrade']->getTrade()->getOpenDateTime()->format('Y-m-d'),
            static fn(int $key, array $formatItem) => intval($formatItem['cumulativeTotal']),
        );
    }
}
