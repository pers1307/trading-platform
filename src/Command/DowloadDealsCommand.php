<?php

namespace App\Command;

use App\Service\StockService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

#[AsCommand(
    name: 'app:dowload-deals',
    description: 'Скачать новые сделки со счетов'
)]
class DowloadDealsCommand extends Command
{
    public function __construct(
//        private readonly bool $isDebug,
//        private readonly StockService $stockService,
//        private readonly LoggerInterface $dictionaryLogger,
    ) {
        parent::__construct();
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * Запускать будем каждые 5 мин.
         */

        /**
         * Подконнектится к счетам
         */

        /**
         * Скачать сделки
         */

        /**
         * Понять какие сделки последние и докачать их
         */

        /**
         * Поместить их в БД
         */

        /**
         * Сплит 2х счетов в один
         */

//        try {
//            $this->stockService->update();
//        } catch (\Throwable $exception) {
//            $this->dictionaryLogger->error($exception);
//
//            if ($this->isDebug) {
//                return Command::FAILURE;
//            }
//        }

        return Command::SUCCESS;
    }
}
