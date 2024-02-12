<?php

namespace App\Controller;

use App\Entity\Strategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StrategyController extends AbstractController
{
    #[Route('/strategies', name: 'app_strategies_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $strategyRepository = $entityManager->getRepository(Strategy::class);
        $strategies = $strategyRepository->findAll();

        dump($strategies);

        return $this->render('strategy/index.html.twig', [
            'strategies' => $strategies,
        ]);
    }
}
