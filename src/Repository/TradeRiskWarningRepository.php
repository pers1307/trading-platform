<?php

namespace App\Repository;

use App\Entity\Trade;
use App\Entity\TradeRiskWarning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TradeRiskWarning>
 * @method TradeRiskWarning|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeRiskWarning|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeRiskWarning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeRiskWarningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeRiskWarning::class);
    }
}
