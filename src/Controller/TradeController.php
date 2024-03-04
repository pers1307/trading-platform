<?php

namespace App\Controller;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Entity\Trade;
use App\Service\TradeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/trades/statistics', name: 'app_trade_statistics')]
    public function statistics(EntityManagerInterface $entityManager): Response
    {
        $tradeRepository = $entityManager->getRepository(Trade::class);
        $trades = $tradeRepository->findBy(['strategy' => 1, 'status' => 'close'], ['openDateTime' => 'ASC']);

        $finamTrades = [];
        $history = 0;
        foreach ($trades as $trade) {
            $stock = $trade->getStock();

            // всегда 1 лот
            // всегда фиксированный риск

            /**
             *
             */

            if ('long' === $trade->getType()) {
                $result = ($trade->getClosePrice() - $trade->getOpenPrice()) * $stock->getLotSize() * $trade->getLots();
                //                $result = ($trade->getClosePrice() - $trade->getOpenPrice()) * $stock->getLotSize() * 1;
            } else {
                $result = ($trade->getOpenPrice() - $trade->getClosePrice()) * $stock->getLotSize() * $trade->getLots();
                //                $result = ($trade->getOpenPrice() - $trade->getClosePrice()) * $stock->getLotSize() * 1;
            }

            $history = $history + $result;

            $finamTrades[] = [
                'trade' => $trade,
                'result' => $result,
                'history' => $history,
            ];
        }

        $profitTrades = 0;
        $lossTrades = 0;

        foreach ($finamTrades as $finamTrade) {
            if ($finamTrade['result'] > 0) {
                ++$profitTrades;
            } else {
                ++$lossTrades;
            }
        }

        //        dd($finamTrades);

        //        $tradeRepository = $entityManager->getRepository(Trade::class);
        //        $trades = $tradeRepository->findAll();

        /**
         * Считать ещё риск / прибыль!
         */

        return $this->render('trades/statistics.html.twig', [
            'finamTrades' => $finamTrades,
            'profitTrades' => $profitTrades,
            'lossTrades' => $lossTrades,
        ]);
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

    #[Route('/trades/strategy/{strategyId<\d+>}/accaunt/{accauntId<\d+>}', name: 'app_trade_strategy_accaunt')]
    public function listByStrategyAndAccaunt(int $strategyId, int $accauntId, EntityManagerInterface $entityManager): Response
    {
        $tradeRepository = $entityManager->getRepository(Trade::class);
        $strategiesByAccaunts = $tradeRepository->getStrategiesByAccaunts();

        return $this->render('trades/list.by.strategies.html.twig', [
            'strategiesByAccaunts' => $strategiesByAccaunts,
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
