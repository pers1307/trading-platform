<?php

namespace App\Controller;

use App\Service\DictionaryStockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DictionaryStockController extends AbstractController
{
    public function __construct(
        private readonly DictionaryStockService $dictionaryStockService
    ) {
    }

    #[Route('/dictionary/stocks', name: 'app_dictionary_stock_index')]
    public function index(): Response
    {
        $stocks = $this->dictionaryStockService->findAll();

        return $this->render('dictionary/stocks/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
}
