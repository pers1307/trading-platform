<?php

namespace App\Command;

use App\Entity\Trade;
use App\Repository\AccauntRepository;
use App\Repository\StockRepository;
use App\Repository\StrategyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:upload-deals',
    description: 'Загрузить сделки'
)]
class UploadDealsCommand extends Command
{
    public function __construct(
        private string $uploadPath,
        private StockRepository $stockRepository,
        private AccauntRepository $accauntRepository,
        private StrategyRepository $strategyRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    private function loadDeals(): array
    {
        $pathToDealFile = $this->uploadPath . '/' . 'deals.csv';
        return array_map('str_getcsv', file($pathToDealFile));
    }

    private function strategyToId(string $strategyName): int
    {
        return match ($strategyName) {
            'Закрытый клуб' => 1,
            'Финам' => 2,
        };
    }

    private function mapDirection(string $direction): string
    {
        return match ($direction) {
            'Long' => 'long',
            'Short' => 'short'
        };
    }

    private function mapStatus(string $status): string
    {
        return match ($status) {
            'Завершена' => 'close',
            'Актуально' => 'open'
        };
    }

    private function formatPrice(string $price): float
    {
        $price = str_replace(" ", "", $price);
        $price = str_replace(",", ".", $price);
        $price = str_replace("\u{A0}", "", $price);

        return floatval($price);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $deals = $this->loadDeals();

        $accaunt = $this->accauntRepository->find(1);

        foreach ($deals as $key => $deal) {
            if (
                $key == 0
                || $deal[2] === 'Аналитика Евгения'
                || $deal[8] == 0
            ) {
                continue;
            }

            $stock = $this->stockRepository->findOneBy(['secId' => $deal[3]]);
            if (is_null($stock)) {
                $io->error("$deal[3] не найден в справочнике");
                continue;
            }

            $strategy = $this->strategyRepository->find($this->strategyToId($deal[2]));

            $trade = new Trade();
            $trade->setType($this->mapDirection($deal[4]));

            $trade->setOpenDateTime(new \DateTime($deal[1]));
            $trade->setOpenPrice($this->formatPrice($deal[5]));

            if ($deal[10] === '****' || empty($deal[10])) {
                $trade->setCloseDateTime(null);
            } else {
                $trade->setCloseDateTime(new \DateTime($deal[10]));
            }

            if (empty($deal[9])) {
                $trade->setClosePrice(null);
            } else {
                $trade->setClosePrice($this->formatPrice($deal[9]));
            }

            $trade->setStopLoss($this->formatPrice($deal[6]));
            $trade->setTakeProfit($this->formatPrice($deal[7]));

            $trade->setLots(intval($deal[8]));

            $trade->setStatus($this->mapStatus($deal[11]));

            $trade->setStrategy($strategy);
            $trade->setAccaunt($accaunt);
            $trade->setStock($stock);

            $this->entityManager->persist($trade);
            $this->entityManager->flush();
//            dd($deal);
        }

        return Command::SUCCESS;
    }
}
