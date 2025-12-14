<?php

namespace App\Command;

use App\Service\CentralBankKeyRateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'inflation:key-rate',
    description: 'Получить текущее значение ключевой ставки ЦБ с сайта cbr.ru'
)]
class GetCentralBankKeyRateCommand extends Command
{
    public function __construct(
        private readonly CentralBankKeyRateService $centralBankKeyRateService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $rate = $this->centralBankKeyRateService->getLatestKeyRate();
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        // Формат как на сайте ЦБ: две цифры после запятой, разделитель — запятая.
        $formattedRate = number_format($rate, 2, ',', '');

        $io->success(sprintf('Ключевая ставка ЦБ: %s%%', $formattedRate));

        return Command::SUCCESS;
    }
}
