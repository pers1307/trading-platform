<?php

namespace App\Controller\Trade;

use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;
use App\Service\ActiveExtensionTradeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends AbstractController
{
    public function __construct(
        private readonly ActiveExtensionTradeService $activeExtensionTradeService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/trades', name: 'app_trade_list')]
    public function list(): Response
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $trades = $tradeRepository->findAll();

        return $this->render('trades/list.html.twig', [
            'trades' => $trades,
        ]);
    }

    /**
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownTypeException
     * @throws StockHasNotPriceException
     * @throws TradeHasUnknownStatusException
     */
    #[Route('/trades/active/group/strategies', name: 'app_trade_active_group_by_strategies_list')]
    public function listActiveGroupByStrategies(): Response
    {
        $activeExtensionTradesByStrategies = $this->activeExtensionTradeService->getByStrategy();

        return $this->render('trades/active.list.by.strategies.html.twig', [
            'activeExtensionTradesByStrategies' => $activeExtensionTradesByStrategies,
        ]);
    }
}
