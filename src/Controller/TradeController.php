<?php

namespace App\Controller;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Entity\Strategy;
use App\Entity\Trade;
use App\Service\StatisticService;
use App\Service\TradeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends AbstractController
{
    public function __construct(
        private readonly TradeService $tradeService,
        private readonly StatisticService $statisticService
    ) {
    }

    #[Route('/trades', name: 'app_trade_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $tradeRepository = $entityManager->getRepository(Trade::class);
        $trades = $tradeRepository->findAll();

        return $this->render('trades/list.html.twig', [
            'trades' => $trades,
        ]);
    }

    #[Route('/trades/strategies', name: 'app_trade_strategy_list')]
    public function listByStrategies(EntityManagerInterface $entityManager): Response
    {
        $tradeRepository = $entityManager->getRepository(Trade::class);
        $strategiesByAccaunts = $tradeRepository->getStrategiesByAccaunts();

        return $this->render('trades/list.by.strategies.html.twig', [
            'strategiesByAccaunts' => $strategiesByAccaunts,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/trades/strategy/{strategyId<\d+>}/accaunt/{accauntId<\d+>}', name: 'app_trade_strategy_accaunt')]
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

        return $this->render('trades/strategy.trades.html.twig', [
            'strategy' => $strategy,
            'accaunt' => $accaunt,
            'extensionTrades' => $extensionTrades,
            'graphDataEncode' => $graphDataEncode,
            'strategyStatistics' => $strategyStatistics,
        ]);
    }

    #[Route('/trades/add', name: 'app_trade_add_form', methods: ['GET'])]
    public function add(EntityManagerInterface $entityManager): Response
    {
        // Форма по добавлению сделки
        return new Response();

        //        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        //        $accaunt = $accauntRepository->find($id);
        //        if (is_null($accaunt)) {
        //            throw new NotFoundHttpException();
        //        }
        //
        //        return $this->render('accaunt_history/add.html.twig', [
        //            'accaunt' => $accaunt,
        //        ]);
    }

    #[Route('/trades/add', name: 'app_trade_add', methods: ['POST'])]
    public function addInBase(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // сохранение этого в БД
        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($id);
        if (is_null($accaunt)) {
            throw new NotFoundHttpException();
        }

        $value = $request->get('value');

        $accauntHistory = new AccauntHistory();
        $accauntHistory->setAccaunt($accaunt);
        $accauntHistory->setValue(floatval($value));

        $entityManager->persist($accauntHistory);
        $entityManager->flush();

        $listUrlByAccauntId = $this->generateUrl('app_accaunt_history_list', ['id' => $accaunt->getId()]);
        return new RedirectResponse($listUrlByAccauntId);
    }

    #[Route('/trades/{id<\d+>}/edit', name: 'app_trade_edit_form', methods: ['GET'])]
    public function edit(int $id, EntityManagerInterface $entityManager): Response
    {
        // Форма по добавлению сделки
        return new Response();

        //        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        //        $accaunt = $accauntRepository->find($id);
        //        if (is_null($accaunt)) {
        //            throw new NotFoundHttpException();
        //        }
        //
        //        return $this->render('accaunt_history/add.html.twig', [
        //            'accaunt' => $accaunt,
        //        ]);
    }

    #[Route('/trades/edit', name: 'app_trade_edit', methods: ['POST'])]
    public function editInBase(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // сохранение этого в БД
        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($id);
        if (is_null($accaunt)) {
            throw new NotFoundHttpException();
        }

        $value = $request->get('value');

        $accauntHistory = new AccauntHistory();
        $accauntHistory->setAccaunt($accaunt);
        $accauntHistory->setValue(floatval($value));

        $entityManager->persist($accauntHistory);
        $entityManager->flush();

        $listUrlByAccauntId = $this->generateUrl('app_accaunt_history_list', ['id' => $accaunt->getId()]);
        return new RedirectResponse($listUrlByAccauntId);
    }

    #[Route('/trades/{id<\d+>}/remove', name: 'app_trade_remove', methods: ['POST'])]
    public function remove(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // сохранение этого в БД
        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($id);
        if (is_null($accaunt)) {
            throw new NotFoundHttpException();
        }

        $value = $request->get('value');

        $accauntHistory = new AccauntHistory();
        $accauntHistory->setAccaunt($accaunt);
        $accauntHistory->setValue(floatval($value));

        $entityManager->persist($accauntHistory);
        $entityManager->flush();

        $listUrlByAccauntId = $this->generateUrl('app_accaunt_history_list', ['id' => $accaunt->getId()]);
        return new RedirectResponse($listUrlByAccauntId);
    }
}
