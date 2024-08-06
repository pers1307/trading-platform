<?php

namespace App\Controller\Deal;

use App\Entity\Trade;
use App\Repository\DealRepository;
use App\Repository\StrategyRepository;
use App\Service\RiskProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    public function __construct(
        private readonly DealRepository $dealRepository,
        private readonly RiskProfileService $riskProfileService,
        private readonly StrategyRepository $strategyRepository,
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
    public function createTrade(int $id, Request $request): Response
    {
        $deal = $this->dealRepository->find($id);

        $riskProfiles = $this->riskProfileService->findByAccaunt($deal->getAccaunt()->getId());
        if (empty($riskProfiles)) {
            throw new NotFoundHttpException();
        }

        $referer = $request->headers->get('referer');

        return $this->render('deals/create.html.twig', [
            'deal' => $deal,
            'referer' => $referer,
            'riskProfiles' => $riskProfiles,
        ]);
    }

    #[Route('/deals/{id<\d+>}/create/trade/save', name: 'app_deal_create_trade_save')]
    public function createTradeSave(int $id, Request $request): Response
    {
        $deal = $this->dealRepository->find($id);
        $price = $request->get('price');
        $strategyId = $request->get('strategyId');
        $strategy = $this->strategyRepository->find($strategyId);

        $stock = $deal->getStock();
        if (is_null($stock)) {
            throw new NotFoundHttpException();
        }

        $trade = (new Trade())
            ->setType($deal->getType())
            ->setOpenDateTime($deal->getDateTime())
            ->setOpenPrice(floatval($price))
            ->setLots($deal->getLots())
            ->setStatus(Trade::STATUS_OPEN)
            ->setStock($stock)
            ->setAccaunt($deal->getAccaunt())
            ->setStrategy($strategy);

        $this->entityManager->persist($trade);
        $this->entityManager->remove($deal);
        $this->entityManager->flush();

        $redirectUrl = $this->generateUrl('app_deal_list');

        return new RedirectResponse($redirectUrl);
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
