<?php

namespace App\Service;

use App\Entity\Stock;
use App\Entity\Trade;
use Doctrine\ORM\EntityManagerInterface;

class TradeService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findAll(): array
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        return $tradeRepository
            ->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.strategy', 'strategy')
            ->innerJoin('t.accaunt', 'accaunt')
//            ->addSelect('stock')
//            ->leftJoin('t.accaunt', 'accaunt')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
