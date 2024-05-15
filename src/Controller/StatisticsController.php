<?php

namespace App\Controller;

use App\Entity\Accaunt;
use App\Entity\Strategy;
use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;
use App\Exception\UnknownStatusException;
use App\Service\ExtensionTradeCollectionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo: нет поведенческих тестов
 */
class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly ExtensionTradeCollectionService $extensionTradeCollectionService
    ) {
    }

    /**
     * @throws UnknownStatusException
     */
    #[Route('/statistics/strategies', name: 'app_statistics_strategies_list')]
    public function listByStrategies(EntityManagerInterface $entityManager): Response
    {
        $tradeRepository = $entityManager->getRepository(Trade::class);
        $strategiesByAccaunts = $tradeRepository->getStrategiesByAccaunts(Trade::STATUS_CLOSE);

        return $this->render('statistics/list.by.strategies.html.twig', [
            'strategiesByAccaunts' => $strategiesByAccaunts,
        ]);
    }

    /**
     * @throws StockHasNotPriceException
     * @throws TradeHasUnknownTypeException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownStatusException
     */
    #[Route('/statistics/strategy/{strategyId<\d+>}/accaunt/{accauntId<\d+>}', name: 'app_statistics_strategy_accaunt')]
    public function listByStrategyAndAccaunt(int $strategyId, int $accauntId, EntityManagerInterface $entityManager): Response
    {
        $strategyRepository = $entityManager->getRepository(Strategy::class);
        $strategy = $strategyRepository->find($strategyId);
        if (is_null($strategy)) {
            throw $this->createNotFoundException('Такой стратегии не существует');
        }

        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($accauntId);
        if (is_null($accaunt)) {
            throw $this->createNotFoundException('Такого счета не существует');
        }

        $extensionTradesCollection = $this->extensionTradeCollectionService->getCollection($strategyId, $accauntId);
        if (empty($extensionTradesCollection->getExtensionTrades())) {
            throw $this->createNotFoundException('Нет сделок по данной стратегии');
        }

        return $this->render('statistics/strategy.trades.html.twig', [
            'strategy' => $strategy,
            'accaunt' => $accaunt,
            'extensionTradesCollection' => $extensionTradesCollection,
        ]);
    }
}
