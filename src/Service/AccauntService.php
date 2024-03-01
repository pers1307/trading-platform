<?php

namespace App\Service;

use App\Entity\Accaunt;
use Doctrine\ORM\EntityManagerInterface;

class AccauntService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findAll(): array
    {
        $stockRepository = $this->entityManager->getRepository(Accaunt::class);
        return $stockRepository->findAll();
    }
}
