<?php

namespace App\Controller;

use App\Service\AccauntService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccauntController extends AbstractController
{
    public function __construct(
        private readonly AccauntService $accauntService
    ) {
    }

    #[Route('/accaunts', name: 'app_accaunts_index')]
    public function index(): Response
    {
        $accaunts = $this->accauntService->findAll();

        return $this->render('accaunt/index.html.twig', [
            'accaunts' => $accaunts,
        ]);
    }
}
