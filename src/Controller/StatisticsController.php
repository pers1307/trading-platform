<?php

namespace App\Controller;

use App\Entity\Accaunt;
use App\Entity\Strategy;
use App\Entity\Trade;
use App\Service\StatisticService;
use App\Service\TradeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly TradeService $tradeService,
        private readonly StatisticService $statisticService
    ) {
    }

    #[Route('/statistics/strategies', name: 'app_statistics_strategies_list')]
    public function listByStrategies(EntityManagerInterface $entityManager): Response
    {
        $tradeRepository = $entityManager->getRepository(Trade::class);
        $strategiesByAccaunts = $tradeRepository->getStrategiesByAccaunts();

        return $this->render('statistics/list.by.strategies.html.twig', [
            'strategiesByAccaunts' => $strategiesByAccaunts,
        ]);
    }

    /**
     * @throws InvalidArgumentException
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

        $extensionTrades = $this->tradeService->getExtensionTrades($strategyId, $accauntId);
        $graphDataEncode = $this->tradeService->formatGraphData($extensionTrades);

        /**
         * @todo форматирование под график тоже перенести
         */
        $strategyStatistics = $this->statisticService->calculate($extensionTrades);

        /**
         * @todo при рефакторинге сделать некие единые фикстуры для юнитов и интеграционных тестов
         */

        /**
         * @todo оптимизировать запросы в модуле
         */

        return $this->render('statistics/strategy.trades.html.twig', [
            'strategy' => $strategy,
            'accaunt' => $accaunt,
            'extensionTrades' => $extensionTrades,
            'graphDataEncode' => $graphDataEncode,
            'strategyStatistics' => $strategyStatistics,
        ]);
    }
}
