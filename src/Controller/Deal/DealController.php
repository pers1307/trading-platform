<?php

namespace App\Controller\Deal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    public function __construct(
//        private readonly ActiveExtensionTradeService $activeExtensionTradeService,
//        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/deals', name: 'app_deal_list')]
    public function list(): Response
    {
        /**
         * Отображаем список сделок, которые подгрузим
         */

//        $tradeRepository = $this->entityManager->getRepository(Trade::class);
//        $trades = $tradeRepository->findAll();

        /**
         * Самые новые сверху!
         */

        return $this->render('deals/list.html.twig', [
//            'trades' => $trades,
        ]);
    }

    #[Route('/deals/transfer/trades', name: 'app_deal_transfer_trade')]
    public function transfer(): Response
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
