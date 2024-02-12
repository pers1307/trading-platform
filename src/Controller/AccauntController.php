<?php

namespace App\Controller;

use App\Entity\Accaunt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccauntController extends AbstractController
{
    #[Route('/accaunts', name: 'app_accaunts_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunts = $accauntRepository->findAll();

        return $this->render('accaunt/index.html.twig', [
            'accaunts' => $accaunts,
        ]);
    }
}
