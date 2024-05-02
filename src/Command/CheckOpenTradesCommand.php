<?php

namespace App\Command;

use App\Repository\TradeRepository;
use App\Service\OpenTradeApplierService;
use App\Service\OpenTradeNotificationService;
use App\Service\OpenTradeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo интеграционный тест
 */
#[AsCommand(
    name: 'app:check-open-trades',
    description: 'Проверить состояние открытых позиций'
)]
class CheckOpenTradesCommand extends Command
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly OpenTradeService $openTradeService,
        private readonly OpenTradeApplierService $openTradeApplierService,
        private readonly OpenTradeNotificationService $openTradeNotificationService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $trades = $this->tradeRepository->findAllActive();

        $openTradeNotifications = $this->openTradeService->check($trades);

        $openTradeNotifications = $this->openTradeNotificationService->merge($openTradeNotifications);
        $this->openTradeApplierService->apply($openTradeNotifications);

        return Command::SUCCESS;
    }
}
