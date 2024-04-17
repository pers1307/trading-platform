<?php

namespace App\Controller;

use App\Entity\RiskProfile;
use App\Entity\Trade;
use App\Service\AccauntService;
use App\Service\CalculateService;
use App\Service\CalculateStateFormService;
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

/**
 * @todo при переходе назад запоминать выбранное ранее значение
 * @todo добавить валидацию параметров. Возможно перенести на post методы
 */
class CalculateController extends AbstractController
{
    public function __construct(
        private readonly AccauntService $accauntService,
        private readonly StockService $stockService,
        private readonly RiskProfileService $riskProfileService,
        private readonly CalculateService $calculateService,
        private readonly LoggerInterface $dictionaryLogger,
        private readonly CalculateStateFormService $calculateStateFormService,
    ) {
    }

    #[Route('/calculate/accaunt', name: 'app_calculate_accaunt_index')]
    public function chooseAccaunt(): Response
    {
        $this->calculateStateFormService->clear();
        $accaunts = $this->accauntService->findAll();

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
        $this->calculateStateFormService->setAccauntId($accauntId);

        return $this->redirectToRoute('app_calculate_strategy');
    }

    #[Route('/calculate/strategy', name: 'app_calculate_strategy')]
    public function chooseStrategy(): Response
    {
        $accauntId = $this->calculateStateFormService->getAccauntId();
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

        $this->calculateStateFormService->setStrategyId($strategyId);

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
        $this->calculateStateFormService->setStockId($stockId);

        try {
            $this->stockService->updateByStockId($stockId);
        } catch (\Throwable $exception) {
            $this->dictionaryLogger->error($exception);
        }

        return $this->redirectToRoute('app_calculate_parameters');
    }

    #[Route('/calculate/parameters', name: 'app_calculate_parameters')]
    public function chooseParameters(): Response
    {
        $stockId = $this->calculateStateFormService->getStockId();
        $accauntId = $this->calculateStateFormService->getAccauntId();
        $strategyId = $this->calculateStateFormService->getStrategyId();

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
        // Получение данных
        // Валидация данных
        // Применение данных

        $stockPrice = $request->get('stockPrice');
        $type = $request->get('type');
        $stopLossPrice = $request->get('stopLossPrice');
        $takeProfitPrice = $request->get('takeProfitPrice');

        $this->calculateStateFormService->setStockPrice($stockPrice);
        $this->calculateStateFormService->setStopLossPrice($stopLossPrice);
        $this->calculateStateFormService->setTakeProfitPrice($takeProfitPrice);
        $this->calculateStateFormService->setType($type);

        return $this->redirectToRoute('app_calculate_result');
    }

    /**
     * @todo вынести в отдельный сервис + тесты
     */
    #[Route('/calculate/result', name: 'app_calculate_result')]
    public function chooseResult(): Response
    {
        $stockId = $this->calculateStateFormService->getStockId();
        $accauntId = $this->calculateStateFormService->getAccauntId();
        $strategyId = $this->calculateStateFormService->getStrategyId();
        $stockPrice = $this->calculateStateFormService->getStockPrice();
        $stopLossPrice = $this->calculateStateFormService->getStopLossPrice();
        $takeProfitPrice = $this->calculateStateFormService->getTakeProfitPrice();
        $type = $this->calculateStateFormService->getType();

        if (empty($stockId) || empty($accauntId) || empty($strategyId) || empty($stockPrice) || empty($type)) {
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
            $lots = $this->calculateService->calculateLotsByDeposit($riskProfile, $stock);
            $persent = $this->calculateService->calculatePersentByDeposit($riskProfile, $stock, $lots);
        } else {
            $trade = (new Trade())
                ->setStock($stock)
                ->setOpenPrice($stock->getPrice())
                ->setStopLoss($stopLossPrice)
                ->setTakeProfit($takeProfitPrice);

            $lots = $this->calculateService->calculateLotsByTrade($riskProfile, $trade);
            $trade->setLots($lots);

            $persent = $this->calculateService->calculatePersentByTrade($riskProfile, $trade);

            if (!empty($trade->getTakeProfit())) {
                $profitLoss = abs($trade->getTakeProfit() - $trade->getOpenPrice()) / abs($trade->getOpenPrice() - $trade->getStopLoss());
            }
        }
        $this->calculateStateFormService->setLots($lots);

        return $this->render('calculate/result.html.twig', [
            'riskProfile' => $riskProfile,
            'stock' => $stock,
            'lots' => $lots,
            'persent' => $persent,
            'trade' => $trade,
            'profitLoss' => $profitLoss,
        ]);
    }

    /**
     * @throws \Exception
     * @todo вынести в отдельный сервис
     * @todo покрыть тестом
     */
    #[Route('/calculate/create/trade', name: 'app_calculate_create_trade')]
    public function createTrade(EntityManagerInterface $entityManager): RedirectResponse
    {
        $stockId = $this->calculateStateFormService->getStockId();
        $accauntId = $this->calculateStateFormService->getAccauntId();
        $strategyId = $this->calculateStateFormService->getStrategyId();
        $stockPrice = $this->calculateStateFormService->getStockPrice();
        $stopLossPrice = $this->calculateStateFormService->getStopLossPrice();
        $takeProfitPrice = $this->calculateStateFormService->getTakeProfitPrice();
        $type = $this->calculateStateFormService->getType();
        $lots = $this->calculateStateFormService->getLots();

        if (empty($stockId) || empty($accauntId) || empty($strategyId) || empty($stockPrice) || empty($lots) || empty($type)) {
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
            ->setType($type)
            ->setLots($lots)
            ->setStatus(Trade::STATUS_OPEN);

        $entityManager->persist($trade);
        $entityManager->flush();

        $this->calculateStateFormService->clear();

        return $this->redirectToRoute('app_trade_active_group_by_strategies_list');
    }
}
