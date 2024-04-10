<?php

namespace App\Command;

use App\Service\OpenTradeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:check-open-trades',
    description: 'Проверить состояние открытых позиций'
)]
class CheckOpenTradesCommand extends Command
{
    public function __construct(
        private readonly OpenTradeService $openTradeService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->openTradeService->check();

        return Command::SUCCESS;
    }
}
