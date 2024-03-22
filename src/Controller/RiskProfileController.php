<?php

namespace App\Controller;

use App\Service\RiskProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RiskProfileController extends AbstractController
{
    public function __construct(
        private readonly RiskProfileService $riskProfileService
    ) {
    }

    #[Route('/risk-profiles', name: 'app_risk_profile_index')]
    public function index(): Response
    {
        $riskProfiles = $this->riskProfileService->findAll();

        return $this->render('risk-profile/index.html.twig', [
            'riskProfiles' => $riskProfiles,
        ]);
    }
}
