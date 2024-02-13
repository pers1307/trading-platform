<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_index')]
    public function index(DashboardService $dashboardService): Response
    {
        if ($this->getParameter('app.isDebug')) {
            $accauntsDataForGraph = $dashboardService->getAccauntsDataForGraph();
        } else {
            $accauntsDataForGraph = $dashboardService->getCachedAccauntsDataForGraph();
        }

        return $this->render('dashboard/index.html.twig', [
            'accauntsDataForGraph' => $accauntsDataForGraph,
        ]);
    }
}
