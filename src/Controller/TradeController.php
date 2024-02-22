<?php

namespace App\Controller;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Entity\Trade;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends AbstractController
{
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
    public function listByStrategies(): Response
    {
        // По стратегиям
        return new Response();

        //        [$accauntHistoryItems, $accaunt, $graphDataEncode] = $accauntHistoryService->getDataByAccauntId($id);
        //
        //        if (is_null($accaunt)) {
        //            throw new NotFoundHttpException();
        //        }
        //
        //        return $this->render('accaunt_history/list.html.twig', [
        //            'accauntHistoryItems' => $accauntHistoryItems,
        //            'accaunt' => $accaunt,
        //            'graphDataEncode' => $graphDataEncode,
        //        ]);
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
