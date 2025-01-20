<?php

namespace App\Controller;

use App\Service\DashboardService;
use App\Service\DashboardStatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly DashboardStatisticService $dashboardStatisticService
    ) {
    }

    #[Route('/', name: 'app_dashboard_index')]
    public function index(): Response
    {
        $accauntsDataForGraph = $this->dashboardService->getAccauntsDataForGraph();
        $statistic = $this->dashboardStatisticService->calculate();

        return $this->render('dashboard/index.html.twig', [
            'accauntsDataForGraph' => $accauntsDataForGraph,
            'statistic' => $statistic,
        ]);
    }
}
