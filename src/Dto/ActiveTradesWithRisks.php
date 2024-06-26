<?php

namespace App\Dto;

use App\Entity\RiskProfile;
use App\Entity\Trade;

class ActiveTradesWithRisks
{
    public function __construct(
        private readonly array $trades,
        private readonly array $indexRiskProfiles,
    ) {
    }

    /**
     * @return Trade[]
     */
    public function getTrades(): array
    {
        return $this->trades;
    }

    /**
     * @return RiskProfile[]
     */
    public function getIndexRiskProfiles(): array
    {
        return $this->indexRiskProfiles;
    }
}
