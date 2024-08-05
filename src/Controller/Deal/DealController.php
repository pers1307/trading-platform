<?php

namespace App\Controller\Deal;

use App\Repository\DealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    public function __construct(
        private readonly DealRepository $dealRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/deals', name: 'app_deal_list')]
    public function list(): Response
    {
        $deals = $this->dealRepository->findAllForList();

        return $this->render('deals/list.html.twig', [
            'deals' => $deals,
        ]);
    }

    #[Route('/deals/{id<\d+>}/create/trade', name: 'app_deal_create_trade')]
    public function createTrade(int $id): Response
    {
        $deal = $this->dealRepository->find($id);

        /**
         * Присвоить счет,
         * подгрузить его автоматом
         */

        /**
         * Присвоить стратегию
         */

        /**
         * Присвоить акцию
         * подгрузить его автоматом
         */

        /**
         * Присвоить тейк
         */

        /**
         * Присвоить стоп лосс
         */

        return $this->render('deals/list.html.twig', [
            //            'trades' => $trades,
        ]);
    }

    #[Route('/deals/create/trade/save', name: 'app_deal_create_trade_save')]
    public function createTradeSave(): Response
    {
        return $this->render('deals/list.html.twig', [
            //            'trades' => $trades,
        ]);
    }

    #[Route('/deals/{id<\d+>}/close/trade', name: 'app_deal_close_trade')]
    public function closeTrade(int $id): Response
    {
        /**
         * Выбрать сделку для закрытия!
         * Подгрузить только открытие сделки
         */

        return $this->render('deals/list.html.twig', [
            //            'trades' => $trades,
        ]);
    }

    #[Route('/deals/close/trade/save', name: 'app_deal_close_trade_save')]
    public function closeTradeSave(): Response
    {

        return $this->render('deals/list.html.twig', [
            //            'trades' => $trades,
        ]);
    }

    /**
     * Переместить сделку в позиции
     * Отдельная страница для этого
     */

    /**
     * Приплюсовать объем к позиции
     */

    /**
     * Удалить сделку (без подтверждения)
     */

    /**
     * Приплюсовать сделку в позицию
     * Отдельная страница для этого
     */
}
