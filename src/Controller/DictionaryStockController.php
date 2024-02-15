<?php

namespace App\Controller;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DictionaryStockController extends AbstractController
{
    #[Route('/dictionary/stocks', name: 'app_dictionary_stock_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stockRepository = $entityManager->getRepository(Stock::class);
        $stocks = $stockRepository->findAll();

        return $this->render('dictionary/stocks/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
}
