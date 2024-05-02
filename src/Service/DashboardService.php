<?php

namespace App\Service;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DashboardService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getCachedAccauntsDataForGraph(): array
    {
        return $this->cache->get('dashboard_accaunts', function (CacheItemInterface $cacheItem) {
            $cacheItem->expiresAfter(60 * 60 * 24);
            return $this->getAccauntsDataForGraph();
        });
    }

    public function getAccauntsDataForGraph(): array
    {
        $accauntRepository = $this->entityManager->getRepository(Accaunt::class);
        $accaunts = $accauntRepository->findAll();

        $result = [];
        foreach ($accaunts as $accaunt) {
            $result[$accaunt->getTitle()] = $this->getFormatGraphDataByAccaunt($accaunt);
        }

        return $result;
    }

    private function getFormatGraphDataByAccaunt(Accaunt $accaunt): string
    {
        $accauntHistoryRepository = $this->entityManager->getRepository(AccauntHistory::class);
        $accauntHistoryItems = $accauntHistoryRepository->findBy(['accaunt' => $accaunt->getId()], ['created' => 'DESC'], 20);

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
