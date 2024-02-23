<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    public function __construct(
        private bool $isDebug
    )
    {
    }

    #[Route('/', name: 'app_dashboard_index')]
    public function index(DashboardService $dashboardService): Response
    {
        if ($this->isDebug) {
            $accauntsDataForGraph = $dashboardService->getAccauntsDataForGraph();
        } else {
            $accauntsDataForGraph = $dashboardService->getCachedAccauntsDataForGraph();
        }

        return $this->render('dashboard/index.html.twig', [
            'accauntsDataForGraph' => $accauntsDataForGraph,
        ]);
    }
}
