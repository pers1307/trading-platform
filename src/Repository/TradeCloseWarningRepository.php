<?php

namespace App\Repository;

use App\Entity\TradeCloseWarning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TradeCloseWarning>
 * @method TradeCloseWarning|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeCloseWarning|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeCloseWarning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeCloseWarningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeCloseWarning::class);
    }
}
