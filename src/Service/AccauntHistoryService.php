<?php

namespace App\Service;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use Doctrine\ORM\EntityManagerInterface;

class AccauntHistoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getDataByAccauntId(int $id): array
    {
        $accauntHistoryRepository = $this->entityManager->getRepository(AccauntHistory::class);
        $accauntHistoryItems = $accauntHistoryRepository->findBy(['accaunt' => $id], ['created' => 'ASC']);

        $accauntRepository = $this->entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($id);

        return [
            $accauntHistoryItems,
            $accaunt,
            $this->formatGraphData($accauntHistoryItems),
        ];
    }

    private function formatGraphData(array $accauntHistoryItems): string
    {
        return json_encode(
            [
                'labels' => array_map(static fn(AccauntHistory $accauntHistory) => $accauntHistory->getCreated()->format('Y-m-d'), $accauntHistoryItems),
                'values' => array_map(
                    static fn(AccauntHistory $accauntHistory) => intval($accauntHistory->getBalance()),
                    $accauntHistoryItems
                ),
            ]
        );
    }
}
