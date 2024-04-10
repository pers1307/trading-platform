<?php

namespace App\Controller;

use App\Entity\RiskProfile;
use App\Entity\Trade;
use App\Event\NotificationEvent;
use App\Service\AccauntService;
use App\Service\CalculateService;
use App\Service\RiskProfileService;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo при переходе назад запоминать выбранное ранее значение
 */
class CalculateController extends AbstractController
{
    public function __construct(
        private readonly AccauntService $accauntService,
        private readonly StockService $stockService,
        private readonly RiskProfileService $riskProfileService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CalculateService $calculateService
    ) {
    }

    #[Route('/calculate/accaunt', name: 'app_calculate_accaunt_index')]
    public function chooseAccaunt(Request $request): Response
    {
        $accaunts = $this->accauntService->findAll();
        $session = $request->getSession();
        $session->clear();

        return $this->render('calculate/choose-accaunt.html.twig', [
            'accaunts' => $accaunts,
        ]);
    }

    #[Route('/calculate/accaunt/save', name: 'app_calculate_accaunt_save')]
    public function saveAccaunt(Request $request): RedirectResponse
    {
        $accauntId = $request->get('accauntId');
        if (empty($accauntId)) {
            throw new NotFoundHttpException();
        }

        $session = $request->getSession();
        $session->set('accauntId', $accauntId);

        return $this->redirectToRoute('app_calculate_strategy');
    }

    #[Route('/calculate/strategy', name: 'app_calculate_strategy')]
    public function chooseStrategy(Request $request): Response
    {
        $session = $request->getSession();
        $accauntId = $session->get('accauntId');
        if (empty($accauntId)) {
            throw new NotFoundHttpException();
        }

        $riskProfiles = $this->riskProfileService->findByAccaunt($accauntId);
        if (empty($riskProfiles)) {
            throw new NotFoundHttpException();
        }

        return $this->render('calculate/choose-strategy.html.twig', [
            'riskProfiles' => $riskProfiles,
        ]);
    }

    #[Route('/calculate/strategy/save', name: 'app_calculate_strategy_save')]
    public function saveStrategy(Request $request): RedirectResponse
    {
        $strategyId = $request->get('strategyId');
        if (is_null($strategyId)) {
            throw new NotFoundHttpException();
        }

        $session = $request->getSession();
        $session->set('strategyId', $strategyId);

        return $this->redirectToRoute('app_calculate_stock');
    }

    #[Route('/calculate/stock', name: 'app_calculate_stock')]
    public function chooseStock(): Response
    {
        $stocks = $this->stockService->findAll();

        return $this->render('calculate/choose-stock.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    #[Route('/calculate/stock/save', name: 'app_calculate_stock_save')]
    public function saveStock(Request $request): RedirectResponse
    {
        $stockId = $request->get('stockId');
        if (is_null($stockId)) {
            throw new NotFoundHttpException();
        }

        $session = $request->getSession();
        $session->set('stockId', $stockId);

        try {
            $this->stockService->updateByStockId($stockId);
        } catch (\Throwable $exception) {
            $notificationEvent = new NotificationEvent(
                "Ошибка обновления",
                "Обновление цены акции не удалось",
            );
            $this->eventDispatcher->dispatch($notificationEvent);
        }

        return $this->redirectToRoute('app_calculate_parameters');
    }

    #[Route('/calculate/parameters', name: 'app_calculate_parameters')]
    public function chooseParameters(Request $request): Response
    {
        $session = $request->getSession();
        $stockId = $session->get('stockId');
        $accauntId = $session->get('accauntId');
        $strategyId = $session->get('strategyId');

        if (empty($stockId) || empty($accauntId) || empty($strategyId)) {
            throw new NotFoundHttpException();
        }

        $riskProfile = $this->riskProfileService->findByAccauntAndStrategy($accauntId, $strategyId);
        if (is_null($riskProfile)) {
            throw new NotFoundHttpException();
        }

        $stock = $this->stockService->find($stockId);
        if (is_null($stock)) {
            throw new NotFoundHttpException();
        }

        return $this->render('calculate/choose-params.html.twig', [
            'riskProfile' => $riskProfile,
            'stock' => $stock,
        ]);
    }

    #[Route('/calculate/parameters/save', name: 'app_calculate_parameters_save')]
    public function saveParameters(Request $request): RedirectResponse
    {
        $stockPrice = $request->get('stockPrice');
        $stopLossPrice = $request->get('stopLossPrice');
        $takeProfitPrice = $request->get('takeProfitPrice');

        $session = $request->getSession();
        $session->set('stockPrice', $stockPrice);
        $session->set('stopLossPrice', $stopLossPrice);
        $session->set('takeProfitPrice', $takeProfitPrice);

        return $this->redirectToRoute('app_calculate_result');
    }

    #[Route('/calculate/result', name: 'app_calculate_result')]
    public function chooseResult(Request $request): Response
    {
        $session = $request->getSession();
        $stockId = $session->get('stockId');
        $accauntId = $session->get('accauntId');
        $strategyId = $session->get('strategyId');
        $stockPrice = $session->get('stockPrice');
        $stopLossPrice = $session->get('stopLossPrice');
        $takeProfitPrice = $session->get('takeProfitPrice');

        if (empty($stockId) || empty($accauntId) || empty($strategyId) || empty($stockPrice)) {
            throw new NotFoundHttpException();
        }

        $riskProfile = $this->riskProfileService->findByAccauntAndStrategy($accauntId, $strategyId);
        if (is_null($riskProfile)) {
            throw new NotFoundHttpException();
        }

        $stock = $this->stockService->find($stockId);
        $stock->setPrice($stockPrice);
        if (is_null($stock)) {
            throw new NotFoundHttpException();
        }

        $profitLoss = 0;
        $trade = null;
        if ($riskProfile->getType() === RiskProfile::TYPE_DEPOSIT) {
            $lots = $this->calculateService->calculateLotsByDepositPersent($riskProfile, $stock);
            $persent = $this->calculateService->calculateByDepositPersent($riskProfile, $stock, $lots);
        } else {
            $trade = (new Trade())
                ->setStock($stock)
                ->setOpenPrice($stock->getPrice())
                ->setStopLoss($stopLossPrice)
                ->setTakeProfit($takeProfitPrice);

            $lots = $this->calculateService->calculateLotsByTradePersent($riskProfile, $trade);
            $trade->setLots($lots);

            $persent = $this->calculateService->calculateByTradePersent($riskProfile, $trade);

            if (!empty($trade->getTakeProfit())) {
                $profitLoss = abs($trade->getTakeProfit() - $trade->getOpenPrice()) / abs($trade->getOpenPrice() - $trade->getStopLoss());
            }
        }
        $session->set('lots', $lots);

        return $this->render('calculate/result.html.twig', [
            'riskProfile' => $riskProfile,
            'stock' => $stock,
            'lots' => $lots,
            'persent' => $persent,
            'trade' => $trade,
            'profitLoss' => $profitLoss,
        ]);
    }

    #[Route('/calculate/create/trade', name: 'app_calculate_create_trade')]
    public function createTrade(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $session = $request->getSession();
        $lots = $session->get('lots');
        $stockId = $session->get('stockId');
        $accauntId = $session->get('accauntId');
        $strategyId = $session->get('strategyId');
        $stockPrice = $session->get('stockPrice');
        $stopLossPrice = $session->get('stopLossPrice');
        $takeProfitPrice = $session->get('takeProfitPrice');

        if (empty($stockId) || empty($accauntId) || empty($strategyId) || empty($stockPrice) || empty($lots)) {
            throw new NotFoundHttpException();
        }

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
            ->setStock($stock)
            ->setAccaunt($riskProfile->getAccaunt())
            ->setStrategy($riskProfile->getStrategy())
            ->setOpenPrice($stock->getPrice())
            ->setStopLoss($stopLossPrice)
            ->setTakeProfit($takeProfitPrice)
            ->setType(Trade::TYPE_LONG) // Пока работает только на покупку
            ->setLots($lots)
            ->setStatus(Trade::STATUS_OPEN);

        $entityManager->persist($trade);
        $entityManager->flush();

        $session->clear();

        return $this->redirectToRoute('app_trade_active_group_by_strategies_list');
    }
}
