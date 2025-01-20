<?php

namespace App\Service;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Repository\AccauntHistoryRepository;
use App\Repository\AccauntRepository;

readonly class DashboardService
{
    public function __construct(
        private AccauntRepository $accauntRepository,
        private AccauntHistoryRepository $accauntHistoryRepository,
    ) {
    }

    public function getAccauntsDataForGraph(): array
    {
        $accaunts = $this->accauntRepository->findAll();

        $result = [];
        foreach ($accaunts as $accaunt) {
            $result[$accaunt->getTitle()] = $this->getFormatGraphDataByAccaunt($accaunt);
        }

        return $result;
    }

    private function getFormatGraphDataByAccaunt(Accaunt $accaunt): string
    {
        $accauntHistoryItems = $this->accauntHistoryRepository->findBy(['accaunt' => $accaunt->getId()], ['created' => 'DESC'], 20);

        return $this->formatGraphData(array_reverse($accauntHistoryItems));
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
