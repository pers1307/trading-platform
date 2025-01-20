<?php

namespace App\Repository;

use App\Entity\Trade;
use App\Exception\UnknownStatusException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
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

    public function findCompletely(int $id): ?Trade
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->addSelect('tradeRiskWarning')
            ->addSelect('tradeCloseWarning')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.strategy', 'strategy')
            ->innerJoin('t.accaunt', 'accaunt')
            ->leftJoin('t.tradeRiskWarning', 'tradeRiskWarning')
            ->leftJoin('t.tradeCloseWarning', 'tradeCloseWarning')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->addSelect('tradeRiskWarning')
            ->addSelect('tradeCloseWarning')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.strategy', 'strategy')
            ->innerJoin('t.accaunt', 'accaunt')
            ->leftJoin('t.tradeRiskWarning', 'tradeRiskWarning')
            ->leftJoin('t.tradeCloseWarning', 'tradeCloseWarning')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllCloseByStrategyIdAndAccauntId(int $strategyId, int $accauntId): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('tradeRiskWarning')
            ->addSelect('tradeCloseWarning')
            ->innerJoin('t.stock', 'stock')
            ->leftJoin('t.tradeRiskWarning', 'tradeRiskWarning')
            ->leftJoin('t.tradeCloseWarning', 'tradeCloseWarning')
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

    public function countClose(): int
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.status = :status')
            ->setParameter('status', Trade::STATUS_CLOSE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findFirst(): Trade
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Trade[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->addSelect('tradeRiskWarning')
            ->addSelect('tradeCloseWarning')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.strategy', 'strategy')
            ->innerJoin('t.accaunt', 'accaunt')
            ->leftJoin('t.tradeRiskWarning', 'tradeRiskWarning')
            ->leftJoin('t.tradeCloseWarning', 'tradeCloseWarning')
            ->andWhere('t.status = :status')
            ->orderBy('t.id', 'ASC')
            ->setParameter('status', Trade::STATUS_OPEN)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Trade[]
     */
    public function findAllActiveByParams(int $accauntId, int $stockId, string $type): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('stock')
            ->addSelect('accaunt')
            ->addSelect('strategy')
            ->innerJoin('t.stock', 'stock')
            ->innerJoin('t.accaunt', 'accaunt')
            ->innerJoin('t.strategy', 'strategy')
            ->where('IDENTITY(t.accaunt) = :accauntId')
            ->andWhere('IDENTITY(t.stock) = :stockId')
            ->andWhere('t.type = :type')
            ->andWhere('t.status = :status')
            ->setParameter('accauntId', $accauntId)
            ->setParameter('stockId', $stockId)
            ->setParameter('type', $type)
            ->setParameter('status', Trade::STATUS_OPEN)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws UnknownStatusException
     */
    public function getStrategiesByAccaunts(string $tradeStatus): array
    {
        if (
            Trade::STATUS_OPEN !== $tradeStatus
            && Trade::STATUS_CLOSE !== $tradeStatus
        ) {
            throw new UnknownStatusException();
        }

        $command = "
                SELECT strategy.title AS strategyTitle, accaunt.title AS accauntTitle, strategy.id AS strategyId, accaunt.id AS accauntId
                FROM (
                    SELECT strategy_id, accaunt_id
                    FROM trade
                    WHERE status = :tradeStatus
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
            ->setParameters(
                new ArrayCollection([
                    new Parameter('tradeStatus', $tradeStatus),
                ])
            )
            ->execute();
    }
}
