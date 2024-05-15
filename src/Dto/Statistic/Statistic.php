<?php

namespace App\Dto\Statistic;

class Statistic
{
    public function __construct(
        private readonly CountStatistic $countStatistic,
        private readonly ExpectedStatistic $expectedStatistic,
        private readonly TimeStatistic $timeStatistic,
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
