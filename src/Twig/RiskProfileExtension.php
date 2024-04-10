<?php

namespace App\Twig;

use App\Entity\RiskProfile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RiskProfileExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('riskProfileType', [$this, 'formatType'], ['is_safe' => ['html']]),
            new TwigFilter('isDepositType', [$this, 'isDeposit']),
        ];
    }

    public function formatType(string $status): string
    {
        if ($status === RiskProfile::TYPE_DEPOSIT) {
            return '<span class="badge bg-primary text-dark">Процент от депозита</span>';
        } elseif ($status === RiskProfile::TYPE_TRADE) {
            return '<span class="badge bg-danger text-dark">Процент от риска в сделке</span>';
        }

        return '<span class="badge bg-warning text-dark">Unknown</span>';
    }

    public function isDeposit(string $status): bool
    {
        return $status === RiskProfile::TYPE_DEPOSIT;
    }
}
