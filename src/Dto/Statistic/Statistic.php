<?php

namespace App\Dto\Statistic;

readonly class Statistic
{
    public function __construct(
        private CountStatistic $countStatistic,
        private ExpectedStatistic $expectedStatistic,
        private TimeStatistic $timeStatistic,
    ) {
    }

    public function getCountStatistic(): CountStatistic
    {
        return $this->countStatistic;
    }

    public function getExpectedStatistic(): ExpectedStatistic
    {
        return $this->expectedStatistic;
    }

    public function getTimeStatistic(): TimeStatistic
    {
        return $this->timeStatistic;
    }
}
