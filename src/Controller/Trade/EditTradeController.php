<?php

namespace App\Controller\Trade;

use App\Entity\Trade;
use App\Service\AccauntService;
use App\Service\CreateTradeStateFormService;
use App\Service\RiskProfileService;
use App\Service\StockService;
use App\Service\StrategyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditTradeController extends AbstractController
{
    public function __construct(
        private readonly AccauntService $accauntService,
        private readonly StockService $stockService,
        private readonly StrategyService $strategyService,
        private readonly RiskProfileService $riskProfileService,
        private readonly EntityManagerInterface $entityManager,
        private readonly CreateTradeStateFormService $createTradeStateFormService,
    ) {
    }

    #[Route('/trades/edit/{id<\d+>}', name: 'app_trade_edit_form')]
    public function edit(int $id, Request $request): Response
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $trade = $tradeRepository->findCompletely($id);
        if (is_null($trade)) {
            throw new NotFoundHttpException();
        }

        $accaunts = $this->accauntService->findAll();
        $stocks = $this->stockService->findAll();
        $strategies = $this->strategyService->findAll();

        $referer = $request->headers->get('referer');
        $request->getSession()->set('refererBeforeRemove', $referer);

        return $this->render('edit-trade/edit.html.twig', [
            'accaunts' => $accaunts,
            'stocks' => $stocks,
            'strategies' => $strategies,
            'trade' => $trade,
            'referer' => $referer,
        ]);
    }

    /**
     * @todo отдельный сервис + тесты
     */
    #[Route('/trades/edit/save', name: 'app_trade_edit_form_save')]
    public function save(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $stockId = $this->createTradeStateFormService->getStockId();
        $accauntId = $this->createTradeStateFormService->getAccauntId();
        $strategyId = $this->createTradeStateFormService->getStrategyId();

        $stockPrice = $request->get('stockPrice');
        $type = $request->get('type');
        $stopLossPrice = empty($request->get('stopLossPrice')) ? null : $request->get('stopLossPrice');
        $takeProfitPrice = empty($request->get('takeProfitPrice')) ? null : $request->get('takeProfitPrice');
        $openDateTime = $request->get('openDateTime');
        $lots = $request->get('lots');

        if (empty($stockId) || empty($accauntId) || empty($strategyId) || empty($stockPrice) || empty($lots) || empty($type)) {
            throw new NotFoundHttpException();
        }

        /**
         * @todo валидация направления, тейка и лосса
         */

        /**
         * @todo валидация риск профиля
         */
        // Сразу произвести все проверки!

        /**
         * @todo обнулить все проверки, которые уже были совершены?
         */

        $riskProfile = $this->riskProfileService->findByAccauntAndStrategy($accauntId, $strategyId);
        if (is_null($riskProfile)) {
            throw new NotFoundHttpException();
        }

        $stock = $this->strategyService->find($stockId);
        $stock->setPrice($stockPrice);
        if (is_null($stock)) {
            throw new NotFoundHttpException();
        }

        $trade = (new Trade())
            ->setOpenDateTime(new \DateTime($openDateTime))
            ->setOpenPrice($stock->getPrice())
            ->setStopLoss($stopLossPrice)
            ->setTakeProfit($takeProfitPrice)
            ->setType($type)
            ->setLots($lots)
            ->setStatus(Trade::STATUS_OPEN)
            ->setStock($stock)
            ->setAccaunt($riskProfile->getAccaunt())
            ->setStrategy($riskProfile->getStrategy());

        $entityManager->persist($trade);
        $entityManager->flush();

        $this->createTradeStateFormService->clear();

        return $this->redirectToRoute('app_trade_active_group_by_strategies_list');
    }
}
