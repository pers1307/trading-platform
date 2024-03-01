<?php

namespace App\Service;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

class DictionaryStockService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findAll(): array
    {
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        return $stockRepository->findAll();
    }
}
