<?php

namespace App\Service;

use App\Entity\Strategy;
use Doctrine\ORM\EntityManagerInterface;

class StrategyService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findAll(): array
    {
        $strategyRepository = $this->entityManager->getRepository(Strategy::class);
        return $strategyRepository->findAll();
    }
}
