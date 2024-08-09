<?php

namespace App\Controller\Trade;

use App\Entity\Trade;
use App\Repository\TradeRepository;
use App\Service\AccauntService;
use App\Service\StockService;
use App\Service\StrategyService;
use DateTime;
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
        private readonly TradeRepository $tradeRepository,
        private readonly EntityManagerInterface $entityManager,
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
    #[Route('/trades/edit/{id<\d+>}/save', name: 'app_trade_edit_form_save')]
    public function save(int $id, Request $request): RedirectResponse
    {
        $closeDateTime = $request->get('closeDateTime');
        $closePrice = $request->get('closePrice');
        $stopLossPrice = $request->get('stopLoss');
        $takeProfitPrice = $request->get('takeProfit');
        $lots = $request->get('lots');
        $status = $request->get('status');

        if (empty($closePrice)) {
            $closePrice = null;
        }
        if (empty($stopLossPrice)) {
            $stopLossPrice = null;
        }
        if (empty($takeProfitPrice)) {
            $takeProfitPrice = null;
        }

        $trade = $this->tradeRepository->find($id);

        if (!is_null($closeDateTime)) {
            $closeDateTime = new DateTime($closeDateTime);
        }
        $trade->setCloseDateTime($closeDateTime);

        $trade
            ->setClosePrice($closePrice)
            ->setStopLoss($stopLossPrice)
            ->setTakeProfit($takeProfitPrice)
            ->setLots($lots)
            ->setStatus($status);

        $this->entityManager->persist($trade);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_trade_active_group_by_strategies_list');
    }
}
