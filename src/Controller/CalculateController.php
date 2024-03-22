<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculateController extends AbstractController
{
    //    public function __construct(
    //        private bool $isDebug
    //    ) {
    //    }

    #[Route('/calculate', name: 'app_calculate_index')]
    public function index(): Response
    {
        return new Response();
        //        if ($this->isDebug) {
        //            $accauntsDataForGraph = $dashboardService->getAccauntsDataForGraph();
        //        } else {
        //            $accauntsDataForGraph = $dashboardService->getCachedAccauntsDataForGraph();
        //        }
        //
        //        return $this->render('dashboard/index.html.twig', [
        //            'accauntsDataForGraph' => $accauntsDataForGraph,
        //        ]);
    }
}
