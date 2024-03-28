<?php

namespace App\Controller;

use App\Entity\RiskProfile;
use App\Entity\Stock;
use App\Service\AccauntService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculateController extends AbstractController
{
    public function __construct(
        private readonly AccauntService $accauntService
    ) {
    }

    #[Route('/calculate/accaunt', name: 'app_calculate_accaunt_index')]
    public function chooseAccaunt(): Response
    {
        $accaunts = $this->accauntService->findAll();

        return $this->render('calculate/choose-accaunt.html.twig', [
            'accaunts' => $accaunts,
        ]);
    }

    #[Route('/calculate/accaunt/save', name: 'app_calculate_accaunt_save')]
    public function saveAccaunt(Request $request): RedirectResponse
    {
        $accauntId = $request->get('accauntId');

        $session = $request->getSession();
        $session->set('accauntId', $accauntId);

        return $this->redirectToRoute('app_calculate_strategy');
    }

    #[Route('/calculate/strategy', name: 'app_calculate_strategy')]
    public function chooseStrategy(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $accauntId = $session->get('accauntId');

        $riskProfileRepository = $entityManager->getRepository(RiskProfile::class);
        $riskProfiles = $riskProfileRepository->findByAccaunt($accauntId);

        return $this->render('calculate/choose-strategy.html.twig', [
            'riskProfiles' => $riskProfiles,
        ]);
    }

    #[Route('/calculate/strategy/save', name: 'app_calculate_strategy_save')]
    public function saveStrategy(Request $request): RedirectResponse
    {
        $strategyId = $request->get('strategyId');

        $session = $request->getSession();
        $session->set('strategyId', $strategyId);

        return $this->redirectToRoute('app_calculate_stock');
    }

    #[Route('/calculate/stock', name: 'app_calculate_stock')]
    public function chooseStock(EntityManagerInterface $entityManager): Response
    {
        $stockRepository = $entityManager->getRepository(Stock::class);
        $stocks = $stockRepository->findAll();

        return $this->render('calculate/choose-stock.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    #[Route('/calculate/stock/save', name: 'app_calculate_stock_save')]
    public function saveStock(Request $request): RedirectResponse
    {
        $stockId = $request->get('stockId');

        $session = $request->getSession();
        $session->set('stockId', $stockId);

        /**
         * В зависимости от риск профиля перемещать на страницу с результатами,
         * либо на страницу с параметрами
         */

        return $this->redirectToRoute('app_calculate_parameters');
    }

    #[Route('/calculate/parameters', name: 'app_calculate_parameters')]
    public function chooseParameters(EntityManagerInterface $entityManager): Response
    {
        $stockRepository = $entityManager->getRepository(Stock::class);
        $stocks = $stockRepository->findAll();

        return $this->render('calculate/choose-params.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    #[Route('/calculate/parameters/save', name: 'app_calculate_parameters_save')]
    public function saveParameters(Request $request): RedirectResponse
    {
        $stockId = $request->get('stockId');

        $session = $request->getSession();
        $session->set('stockId', $stockId);

        //        return $this->redirectToRoute('app_calculate_strategy');
        return $this->redirectToRoute('app_calculate_result');
    }

    #[Route('/calculate/result', name: 'app_calculate_result')]
    public function chooseResult(Request $request, EntityManagerInterface $entityManager): Response
    {
        /**
         * @todo рассчет, сколько лотов необходимо
         */
        $session = $request->getSession();
        $stockId = $session->get('stockId');
        $accauntId = $session->get('accauntId');
        $strategyId = $session->get('strategyId');

        /**
         * Передать параметры
         */



        /**
         * Получить кол - во лотов
         */

        //        $stockRepository = $entityManager->getRepository(Stock::class);
        //        $stocks = $stockRepository->findAll();

        return $this->render('calculate/result.html.twig', [
            //            'stocks' => $stocks,
        ]);
    }

    /**
     * @todo перевод в активную сделку
     * @todo обновление инструмента в фоне
     */

    //    #[Route('/calculate/parameters/save', name: 'app_calculate_result')]
    //    public function saveParameters(Request $request): RedirectResponse
    //    {
    //        $stockId = $request->get('stockId');
    //
    //        $session = $request->getSession();
    //        $session->set('stockId', $stockId);
    //
    //        return $this->redirectToRoute('app_calculate_strategy');
    //    }
}
