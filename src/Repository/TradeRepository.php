<?php

namespace App\Repository;

use App\Entity\Trade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trade>
 * @method Trade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trade::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.strategy', 'strategy')
            ->innerJoin('t.accaunt', 'accaunt')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getStrategiesByAccaunts(): array
    {
        $command = "
                SELECT strategy.title AS strategyTitle, accaunt.title AS accauntTitle, strategy.id AS strategyId, accaunt.id AS accauntId
                FROM (
                    SELECT strategy_id, accaunt_id
                    FROM trade
                    GROUP BY strategy_id, accaunt_id    
                ) AS strategiesByAccaunts
                JOIN strategy ON strategiesByAccaunts.strategy_id = strategy.id
                JOIN accaunt ON strategiesByAccaunts.accaunt_id = accaunt.id
            ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('strategyTitle', 'strategyTitle');
        $rsm->addScalarResult('accauntTitle', 'accauntTitle');
        $rsm->addScalarResult('strategyId', 'strategyId', 'integer');
        $rsm->addScalarResult('accauntId', 'accauntId', 'integer');

        return $this->getEntityManager()
            ->createNativeQuery($command, $rsm)
            ->execute();
    }

    //    /**
    //     * @return VinylMix[] Returns an array of VinylMix objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VinylMix
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
