<?php

namespace App\Service;

use App\Dto\ActiveExtensionTradesByStrategy;
use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActiveExtensionTradeService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExtensionTradeService $extensionTradeService,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @return ActiveExtensionTradesByStrategy[]
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownTypeException
     * @throws StockHasNotPriceException
     * @throws TradeHasUnknownStatusException
     */
    public function getByStrategy(): array
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $strategiesByAccaunts = $tradeRepository->getStrategiesByAccaunts();
        $activeTrades = $tradeRepository->findAllActive();
        $activeExtensionTrades = $this->extensionTradeService->convertTradesToExtensionTrades($activeTrades);

        $activeExtensionTradesByStrategy = [];
        foreach ($strategiesByAccaunts as $strategiesByAccaunt) {
            $filterActiveExtensionTrades = [];
            foreach ($activeExtensionTrades as $activeExtensionTrade) {
                if (
                    $strategiesByAccaunt['strategyId'] === $activeExtensionTrade->getTrade()->getStrategy()->getId()
                    && $strategiesByAccaunt['accauntId'] === $activeExtensionTrade->getTrade()->getAccaunt()->getId()
                ) {
                    $filterActiveExtensionTrades[] = $activeExtensionTrade;
                }
            }

            if (!empty($filterActiveExtensionTrades)) {
                $activeExtensionTradesByStrategy[] = new ActiveExtensionTradesByStrategy(
                    $filterActiveExtensionTrades,
                    $filterActiveExtensionTrades[0]->getTrade()->getStrategy(),
                    $filterActiveExtensionTrades[0]->getTrade()->getAccaunt()
                );
            }
        }

        return $activeExtensionTradesByStrategy;
    }
}
