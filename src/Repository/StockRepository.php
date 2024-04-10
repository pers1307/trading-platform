<?php

namespace App\Repository;

use App\Dto\MoexStock;
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

    public function upsertByMoexStock(MoexStock $moexStock): void
    {
        $command = "
                INSERT INTO `stock` 
                    (title, sec_id, lot_size, min_step, price, open, high, low)
                VALUES 
                    (:title, :secId, :lotSize, :minStep, :price, :open, :high, :low)
                ON DUPLICATE KEY UPDATE
                    lot_size = VALUES(lot_size),
                    min_step = VALUES(min_step),
                    price = VALUES(price),
                    open = VALUES(open),
                    high = VALUES(high),
                    low = VALUES(low);
            ";
        
        $this->entityManager
            ->createNativeQuery($command, new ResultSetMapping())
            ->setParameter('title', $moexStock->getTitle())
            ->setParameter('secId', $moexStock->getSecId())
            ->setParameter('lotSize', $moexStock->getLotSize())
            ->setParameter('minStep', $moexStock->getMinStep())
            ->setParameter('price', $moexStock->getPrice())
            ->setParameter('open', $moexStock->getOpen())
            ->setParameter('high', $moexStock->getHigh())
            ->setParameter('low', $moexStock->getLow())
            ->execute();
    }
}
