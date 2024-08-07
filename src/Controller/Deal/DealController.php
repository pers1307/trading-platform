<?php

namespace App\Controller\Deal;

use App\Entity\Deal;
use App\Entity\Trade;
use App\Repository\DealRepository;
use App\Repository\StrategyRepository;
use App\Repository\TradeRepository;
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
        private readonly TradeRepository $tradeRepository,
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
    public function closeTrade(int $id, Request $request): Response
    {
        $deal = $this->dealRepository->find($id);
        if (is_null($deal->getStock())) {
            throw new \Exception("Нельзя закрыть позицию с неопределенным инструментом");
        }

        $type = Trade::TYPE_LONG;
        if ($deal->getType() === Deal::TYPE_LONG) {
            $type = Trade::TYPE_SHORT;
        }

        $trades = $this->tradeRepository->findAllActiveByParams(
            $deal->getAccaunt()->getId(),
            $deal->getStock()->getId(),
            $type
        );

        $formatTrades = [];
        foreach ($trades as $trade) {
            $type = ucfirst($trade->getType());
            $formatTrades[$trade->getId()] =
                "{$trade->getStrategy()->getTitle()}. {$trade->getAccaunt()->getTitle()}. {$trade->getStock()->getSecId()}. $type. {$trade->getLots()} лот";
        }

        $referer = $request->headers->get('referer');
        return $this->render('deals/close.html.twig', [
            'deal' => $deal,
            'referer' => $referer,
            'formatTrades' => $formatTrades,
        ]);
    }

    #[Route('/deals/{id<\d+>}/close/trade/save', name: 'app_deal_close_trade_save')]
    public function closeTradeSave(int $id, Request $request): Response
    {
        $deal = $this->dealRepository->find($id);
        $price = $request->get('price');
        $tradeId = $request->get('tradeId');

        $trade = $this->tradeRepository->find($tradeId);
        if (is_null($trade)) {
            throw new NotFoundHttpException();
        }

        $trade
            ->setCloseDateTime($deal->getDateTime())
            ->setClosePrice(floatval($price))
            ->setStatus(Trade::STATUS_CLOSE);

        $this->entityManager->persist($trade);
        $this->entityManager->remove($deal);
        $this->entityManager->flush();

        $redirectUrl = $this->generateUrl('app_deal_list');
        return new RedirectResponse($redirectUrl);
    }

    /**
     * Удалить сделку (без подтверждения)
     */

    /**
     * Приплюсовать сделку в позицию
     * Отдельная страница для этого
     */
}
