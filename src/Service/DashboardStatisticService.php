<?php

namespace App\Service;

use App\Dto\Statistic\CountStatistic;
use App\Dto\Statistic\ExpectedStatistic;
use App\Dto\Statistic\Statistic;
use App\Dto\Statistic\TimeStatistic;
use App\Repository\TradeRepository;
use Monolog\DateTimeImmutable;

readonly class DashboardStatisticService
{
    private DateTimeImmutable $nowDateTime;

    public function __construct(
        string $now,
        private TradeRepository $tradeRepository
    ) {
        $this->nowDateTime = new DateTimeImmutable($now);
    }

    public function calculate(): Statistic
    {
        return new Statistic(
            $this->calculateCountTrades(),
            new ExpectedStatistic(
                0,
                0,
                0,
            ),
            $this->calculateAverageTimeTrades()
        );
    }

    private function calculateCountTrades(): CountStatistic
    {
        $countCloseTrades = $this->tradeRepository->countClose();

        return new CountStatistic(
            $countCloseTrades,
            0,
            0,
            0,
            0
        );
    }

    public function calculateAverageTimeTrades(): TimeStatistic
    {
        $firstTrade = $this->tradeRepository->findFirst();

        $totalTimeIntervalInSecond = round(
            $this->nowDateTime->getTimestamp()
            - $firstTrade->getOpenDateTime()->getTimestamp()
        );

        return new TimeStatistic(
            $totalTimeIntervalInSecond,
            0,
            0,
            0
        );
    }
}
