<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Stock::class);
    }

    public function upsertSecurity(array $securityData): void
    {
        foreach ($securityData as $securityDataItem) {
            $command = "
                INSERT INTO `stock` 
                    (title, sec_id, lot_size, min_step)
                VALUES 
                    (:title, :secId, :lotSize, :minStep)
                ON DUPLICATE KEY UPDATE
                    lot_size = VALUES(lot_size),
                    min_step = VALUES(min_step);
            ";

            $this->entityManager
                ->createNativeQuery($command, new ResultSetMapping())
                ->setParameter('title', $securityDataItem[9])
                ->setParameter('secId', $securityDataItem[0])
                ->setParameter('lotSize', $securityDataItem[4])
                ->setParameter('minStep', $securityDataItem[14])
                ->execute();
        }
    }

    public function updatePrices(array $marketData): void
    {
        foreach ($marketData as $marketDataItem) {
            $this->entityManager->createQueryBuilder()
                ->update(Stock::class, 's')
                ->set('s.price', ':price')
                ->where('s.secId = :secId')
                ->setParameter('price', $marketDataItem[12])
                ->setParameter('secId', $marketDataItem[0])
                ->getQuery()
                ->execute();
        }
    }
}
