<?php

namespace App\Command;

use App\Service\CheckRiskService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:check-risk',
    description: 'Проверить соответствие рискам'
)]
class CheckRiskCommand extends Command
{
    public function __construct(
        private readonly CheckRiskService $checkRiskService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->checkRiskService->checkAllOpenTrade();
        
        return Command::SUCCESS;
    }
}
