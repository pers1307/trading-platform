<?php

namespace App\Command;

use App\Dto\ActiveTradesWithRisks;
use App\Entity\Trade;
use App\Service\RiskProfileService;
use App\Service\RiskTradeApplierService;
use App\Service\RiskTradeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo интеграционный тест
 */
#[AsCommand(
    name: 'app:risk-trade-check',
    description: 'Проверить все открытые позиции на соответствие рискам'
)]
class RiskTradesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RiskProfileService $riskProfileService,
        private readonly RiskTradeService $riskTradeService,
        private readonly RiskTradeApplierService $riskTradeApplierService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $activeTrades = $tradeRepository->findAllActive();
        $indexRiskProfile = $this->riskProfileService->getIndexAll();

        $activeTradesWithRisks = new ActiveTradesWithRisks(
            $activeTrades,
            $indexRiskProfile
        );

        $riskTrades = $this->riskTradeService->check($activeTradesWithRisks);

        $this->riskTradeApplierService->apply($riskTrades);

        return Command::SUCCESS;
    }
}
