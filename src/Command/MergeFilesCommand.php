<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:merge-files',
    description: 'Смержить файлы'
)]
class MergeFilesCommand extends Command
{
    public function __construct(
        private string $uploadPath
    ) {
        parent::__construct();
    }

    private function findAllDealsFromArrays(string $ticket, array $deals): array
    {
        $result = [];
        foreach ($deals as $deal) {
            if ($deal[5] == $ticket && empty($deal[3])) {
                $result[] = $deal;
            }
        }

        return $result;
    }

    private function aggregate(array $deals): array
    {
        $result = [];
        foreach ($deals as $deal) {
            $key = $deal[0] . ' ' . $deal[1] . ' ' . $deal[2];
            if (isset($result[$key])) {
                $result[$key][7] = $result[$key][7] + $deal[7];
            } else {
                $result[$key] = $deal;
            }
        }

        return $result;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pathToDealFile = $this->uploadPath . '/' . 'deals.csv';
        $deals = array_map('str_getcsv', file($pathToDealFile));

        $pathToSourceOneFile = $this->uploadPath . '/' . '6226d10e-8148-4d86-9e89-8e769ed94bcb.csv';
        $sourceOneDeals = array_map('str_getcsv', file($pathToSourceOneFile));

        $pathToSourceTwoFile = $this->uploadPath . '/' . 'c33f46f8-b728-4d67-bf1a-c20d2c114a5d.csv';
        $sourceTwoDeals = array_map('str_getcsv', file($pathToSourceTwoFile));

        $aggregateDeals = [];
        foreach ($deals as $key => $deal) {
            if ($key == 0) {
                continue;
            }

            if (!empty($deal[10]) && $deal[11] === 'Завершена') {
                $aggregateDeals[] = $deal;
                continue;
            }

            if ($deal[11] === 'Актуально') {
                $aggregateDeals[] = $deal;
                continue;
            }

            dump($deal[3]);

//            $deals1 = $this->findAllDealsFromArrays($deal[3], $sourceOneDeals);
//            dump($deals1);

            $findSourceTwoDeals = $this->findAllDealsFromArrays($deal[3], $sourceTwoDeals);
            $aggreagateSourceTwoDeals = $this->aggregate($findSourceTwoDeals);

            $aggreagateSourceTwoDeals = array_map(static function($item) use ($deal) {
                $dateTime = new \DateTime($item[0] . ' ' . $item[1]);

                $item['formatdateTime'] = $dateTime->format('d.m.Y H:i:s');
                $item['formatPrice'] = floatval($item[7]) / floatval($deal[8]);
                return $item;
            }, $aggreagateSourceTwoDeals);

            $aggreagateSourceTwoDeals = array_filter($aggreagateSourceTwoDeals, static function($item) use ($deal) {
                $dealDateTime = (new \DateTime($deal[1]))->setTime(0,0,0)->format('Y-m-d H:i:s');
                $historyDateTime = (new \DateTime($item[0] . ' ' . $item[1]))->format('Y-m-d H:i:s');

                dump($dealDateTime);
                dump($historyDateTime);
                dump($historyDateTime > $dealDateTime);

                return $historyDateTime > $dealDateTime;
            });

            $aggreagateSourceTwoDeals = array_reverse($aggreagateSourceTwoDeals);

            dump($aggreagateSourceTwoDeals);

//            if ($deal[4] === 'Long') {
//                $filteredSourceTwoDeals = array_filter($aggreagateSourceTwoDeals, static fn($item) => $item[2] === 'Продажа актива');
//            } else {
//                $filteredSourceTwoDeals = array_filter($aggreagateSourceTwoDeals, static fn($item) => $item[2] === 'Покупка актива');
//            }
//            dump($filteredSourceTwoDeals);




            /**
             * Форматирование времени
             */

            /**
             * Форматирование цены
             */


//            foreach ($aggreagateSourceTwoDeals as $aggreagateSourceTwoDeals) {
//                if ($deal[4] === 'Long' && $aggreagateSourceTwoDeals) {
//
//                }
//
//
//            }



            // Найти по дате
            //проверить цену входа

            // Ищем обратную сделку
            // считаем у неё цену выхода

            // Она совпадет?
            // Да? - вписываем отформатированную дату

            // Его нет? Предоставляем выбор!

            if (empty($deals1) && empty($deals2)) {
                $io->error("$deal[3] не найден в файлах сделок");
                dump($deal);
                break;
            }

            die();
        }

        // Зафиксировать все в конечном файле
//        if (!empty($aggregateDeals)) {
//            $pathToFile = $this->uploadPath . '/' . time() . '-' . 'total_deals';
//            $fp = fopen($pathToFile, 'w');
//            foreach ($aggregateDeals as $aggregateDeal) {
//                fputcsv($fp, $aggregateDeal);
//            }
//            fclose($fp);
//        }

        //        dd($deals);

        //        $this->get

        //        $filesystem = new Filesystem();

        //        $filesystem->

        //        $pathToFile = Path::normalize(sys_get_temp_dir().'/'.random_int(0, 1000)),

        //        $pathToFile = \Yii::getAlias($this::UPLOAD_FILE_DIR) . '/' . $fileName;
        //        if (!file_exists($pathToFile)) {
        //            throw new NotFoundFileException();
        //        }
        //
        //        return array_map('str_getcsv', file($pathToFile));

        /**
         * Для каждой позиции сканируем файлы с позициями и мержим их
         */

        /**
         * Выплевываем все в конечный файл
         */

        /**
         * @todo считать файл и занести его в БД
         */

        return Command::SUCCESS;
    }
}
