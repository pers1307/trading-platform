<?php

namespace App\Controller\Trade;

use App\Entity\Trade;
use App\Service\AccauntService;
use App\Service\CreateTradeStateFormService;
use App\Service\RiskProfileService;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CreateTradeController extends AbstractController
{
    public function __construct(
        private readonly AccauntService $accauntService,
        private readonly StockService $stockService,
        private readonly LoggerInterface $dictionaryLogger,
        private readonly RiskProfileService $riskProfileService,
        private readonly CreateTradeStateFormService $createTradeStateFormService,
    ) {
    }

    #[Route('/trades/add/accaunt', name: 'app_trade_add_accaunt_form')]
    public function chooseAccaunt(): Response
    {
        $this->createTradeStateFormService->clear();
        $accaunts = $this->accauntService->findAll();

        return $this->render('create-trade/choose-accaunt.html.twig', [
            'accaunts' => $accaunts,
        ]);
    }

    #[Route('/trades/add/accaunt/save', name: 'app_trade_add_accaunt_form_save')]
    public function saveAccaunt(Request $request): RedirectResponse
    {
        $accauntId = $request->get('accauntId');
        if (empty($accauntId)) {
            throw new NotFoundHttpException();
        }
        $this->createTradeStateFormService->setAccauntId($accauntId);

        return $this->redirectToRoute('app_trade_add_strategy_form');
    }

    #[Route('/trades/add/strategy', name: 'app_trade_add_strategy_form')]
    public function chooseStrategy(): Response
    {
        $accauntId = $this->createTradeStateFormService->getAccauntId();
        if (empty($accauntId)) {
            throw new NotFoundHttpException();
        }

        $riskProfiles = $this->riskProfileService->findByAccaunt($accauntId);
        if (empty($riskProfiles)) {
            throw new NotFoundHttpException();
        }

        return $this->render('create-trade/choose-strategy.html.twig', [
            'riskProfiles' => $riskProfiles,
        ]);
    }

    #[Route('/trades/add/strategy/save', name: 'app_trade_add_strategy_form_save')]
    public function saveStrategy(Request $request): RedirectResponse
    {
        $strategyId = $request->get('strategyId');
        if (is_null($strategyId)) {
            throw new NotFoundHttpException();
        }

        $this->createTradeStateFormService->setStrategyId($strategyId);

        return $this->redirectToRoute('app_trade_add_stock_form');
    }

    #[Route('/trades/add/stock', name: 'app_trade_add_stock_form')]
    public function chooseStock(): Response
    {
        $stocks = $this->stockService->findAll();

        return $this->render('create-trade/choose-stock.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    #[Route('/trades/add/stock/save', name: 'app_trade_add_stock_form_save')]
    public function saveStock(Request $request): RedirectResponse
    {
        $stockId = $request->get('stockId');
        if (is_null($stockId)) {
            throw new NotFoundHttpException();
        }
        $this->createTradeStateFormService->setStockId($stockId);

        try {
            $this->stockService->updateByStockId($stockId);
        } catch (\Throwable $exception) {
            $this->dictionaryLogger->error($exception);
        }

        return $this->redirectToRoute('app_trade_add_parameters_form');
    }

    #[Route('/trades/add/parameters', name: 'app_trade_add_parameters_form')]
    public function chooseParameters(): Response
    {
        $stockId = $this->createTradeStateFormService->getStockId();
        $accauntId = $this->createTradeStateFormService->getAccauntId();
        $strategyId = $this->createTradeStateFormService->getStrategyId();

        if (empty($stockId) || empty($accauntId) || empty($strategyId)) {
            throw new NotFoundHttpException();
        }

        $stock = $this->stockService->find($stockId);
        if (is_null($stock)) {
            throw new NotFoundHttpException();
        }

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        return $this->render('create-trade/choose-params.html.twig', [
            'stock' => $stock,
            'now' => $now,
        ]);
    }

    /**
     * @todo отдельный сервис + тесты
     */
    #[Route('/trades/add/save', name: 'app_trade_add_parameters_form_save')]
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

        $riskProfile = $this->riskProfileService->findByAccauntAndStrategy($accauntId, $strategyId);
        if (is_null($riskProfile)) {
            throw new NotFoundHttpException();
        }

        $stock = $this->stockService->find($stockId);
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
