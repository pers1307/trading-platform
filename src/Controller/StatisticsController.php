<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;
use App\Exception\UnknownStatusException;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntRepository;
use App\Repository\StrategyRepository;
use App\Repository\TradeRepository;
use App\Service\AccauntInflation\AccauntInflationGraphDataFormatter;
use App\Service\ExtensionTradeCollectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly ExtensionTradeCollectionService $extensionTradeCollectionService,
        private readonly TradeRepository $tradeRepository,
        private readonly StrategyRepository $strategyRepository,
        private readonly AccauntRepository $accauntRepository,
        private readonly AccauntInflationRepository $accauntInflationRepository,
        private readonly AccauntInflationGraphDataFormatter $accauntInflationGraphDataFormatter
    ) {
    }

    /**
     * @throws UnknownStatusException
     */
    #[Route('/statistics/strategies', name: 'app_statistics_strategies_list')]
    public function listByStrategies(): Response
    {
        $strategiesByAccaunts = $this->tradeRepository->getStrategiesByAccaunts(Trade::STATUS_CLOSE);

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
    public function listByStrategyAndAccaunt(int $strategyId, int $accauntId): Response
    {
        $strategy = $this->strategyRepository->find($strategyId);
        if (is_null($strategy)) {
            throw $this->createNotFoundException('Такой стратегии не существует');
        }

        $accaunt = $this->accauntRepository->find($accauntId);
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

    /**
     * @throws UnknownStatusException
     */
    #[Route('/statistics/accaunts', name: 'app_statistics_accaunts_list')]
    public function listByAccaunts(): Response
    {
        $accaunts = $this->accauntRepository->findAll();

        return $this->render('statistics/list.by.accaunts.html.twig', [
            'accaunts' => $accaunts,
        ]);
    }

    #[Route('/statistics/accaunt/{id<\d+>}', name: 'app_statistics_accaunt')]
    public function accauntInflation(int $id): Response
    {
        $accaunt = $this->accauntRepository->find($id);
        if (is_null($accaunt)) {
            throw $this->createNotFoundException('Такого счета не существует');
        }

        $accauntInflationItems = $this->accauntInflationRepository->findByAccauntIdOrdered($id);
        $graphDataEncode = $this->accauntInflationGraphDataFormatter->format($accauntInflationItems);

        return $this->render('statistics/accaunt.inflation.html.twig', [
            'accaunt' => $accaunt,
            'accauntInflationItems' => $accauntInflationItems,
            'graphDataEncode' => $graphDataEncode,
        ]);
    }
}
