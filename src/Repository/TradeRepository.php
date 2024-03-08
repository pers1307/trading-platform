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

    public function findAllCloseByStrategyIdAndAccauntId(int $strategyId, int $accauntId): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->innerJoin('t.stock', 'stock')
            ->where('IDENTITY(t.strategy) = :strategyId')
            ->andWhere('IDENTITY(t.accaunt) = :accauntId')
            ->andWhere('t.status = :status')
            ->orderBy('t.id', 'ASC')
            ->setParameter('strategyId', $strategyId)
            ->setParameter('accauntId', $accauntId)
            ->setParameter('status', Trade::STATUS_CLOSE)
            ->getQuery()
            ->getResult();
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.strategy', 'strategy')
            ->innerJoin('t.accaunt', 'accaunt')
            ->andWhere('t.status = :status')
            ->orderBy('t.id', 'ASC')
            ->setParameter('status', Trade::STATUS_OPEN)
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
}
