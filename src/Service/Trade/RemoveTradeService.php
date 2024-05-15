<?php

namespace App\Service\Trade;

use App\Entity\Trade;
use App\Exception\NotFoundTradeException;
use Doctrine\ORM\EntityManagerInterface;

class RemoveTradeService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws NotFoundTradeException
     */
    public function removeById(int $tradeId): void
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $trade = $tradeRepository->find($tradeId);
        if (is_null($trade)) {
            throw new NotFoundTradeException();
        }

        $this->entityManager->remove($trade);
        $this->entityManager->flush();
    }
}
