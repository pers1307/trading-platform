<?php

namespace App\Command;

use App\Entity\AccauntInflation;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntRepository;
use App\Service\AccauntInflation\AccauntInflationCalculator;
use App\Service\AccauntInflation\AccauntInflationRequest;
use App\Service\CentralBankKeyRateService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'inflation:calculate',
    description: 'Рассчитать показатели счета с поправкой на инфляцию'
)]
class InflationCalculateCommand extends Command
{
    public function __construct(
        private readonly AccauntRepository $accauntRepository,
        private readonly AccauntInflationRepository $accauntInflationRepository,
        private readonly AccauntInflationCalculator $accauntInflationCalculator,
        private readonly CentralBankKeyRateService $centralBankKeyRateService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('movement_amount', InputArgument::REQUIRED, 'Движение средств за период')
            ->addArgument('accaunt_id', InputArgument::REQUIRED, 'Идентификатор счета')
            ->addArgument('deposit_rate', InputArgument::REQUIRED, 'Ставка по банковскому депозиту, % годовых')
            ->addArgument('date', InputArgument::OPTIONAL, 'Дата среза в формате Y-m-d (по умолчанию: текущая дата)')
            ->addArgument(
                'central_bank_key_rate',
                InputArgument::OPTIONAL,
                'Ключевая ставка ЦБ, % годовых (если не задана — берется через CentralBankKeyRateService)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $movementAmount = (float) $input->getArgument('movement_amount');
        $accauntId = (int) $input->getArgument('accaunt_id');
        $depositRateArgument = $input->getArgument('deposit_rate');
        $dateString = $input->getArgument('date');
        $centralBankKeyRateArgument = $input->getArgument('central_bank_key_rate');

        $accaunt = $this->accauntRepository->find($accauntId);
        if ($accaunt === null) {
            $io->error(sprintf('Счет с id=%d не найден.', $accauntId));

            return Command::FAILURE;
        }

        $date = $this->resolveSliceDate($dateString, $io);
        if ($date === null) {
            return Command::FAILURE;
        }

        $depositRate = $this->resolveDepositRate($depositRateArgument, $io);
        if ($depositRate === false) {
            return Command::FAILURE;
        }

        $centralBankKeyRate = $this->resolveCentralBankKeyRate($centralBankKeyRateArgument, $io);
        if ($centralBankKeyRate === false) {
            return Command::FAILURE;
        }

        if ($centralBankKeyRate === null) {
            try {
                $centralBankKeyRate = $this->centralBankKeyRateService->getLatestKeyRate();
            } catch (\Throwable $exception) {
                $io->error($exception->getMessage());

                return Command::FAILURE;
            }
        }

        try {
            $accauntInflationResponse = $this->accauntInflationCalculator->calculate(
                new AccauntInflationRequest(
                    accaunt: $accaunt,
                    movementAmount: $movementAmount,
                    date: $date,
                    depositRate: $depositRate,
                    centralBankKeyRate: $centralBankKeyRate,
                )
            );
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $existing = $this->accauntInflationRepository->findOneBy([
            'accaunt' => $accaunt,
            'date' => $accauntInflationResponse->getDate(),
        ]);

        if ($existing === null) {
            $existing = new AccauntInflation();
            $existing->setAccaunt($accaunt);
            $existing->setDate($accauntInflationResponse->getDate());
            $this->entityManager->persist($existing);
        }

        $existing
            ->setMovementAmount($accauntInflationResponse->getMovementAmount())
            ->setCentralBankKeyRate($accauntInflationResponse->getCentralBankKeyRate())
            ->setDepositRate($accauntInflationResponse->getDepositRate())
            ->setAccauntBalance($accauntInflationResponse->getAccauntBalance())
            ->setAccauntInflationBalance($accauntInflationResponse->getAccauntInflationBalance())
            ->setAccauntDepositBalance($accauntInflationResponse->getAccauntDepositBalance());

        $this->entityManager->flush();

        $io->success(sprintf(
            'Счет %d (%s): ставка ЦБ %0.2f%%, инфляц. баланс %0.2f, депозит %0.2f, номинал %0.2f',
            $accauntId,
            $accauntInflationResponse->getDate()->format('Y-m-d'),
            $accauntInflationResponse->getCentralBankKeyRate(),
            $accauntInflationResponse->getAccauntInflationBalance(),
            $accauntInflationResponse->getAccauntDepositBalance(),
            $accauntInflationResponse->getAccauntBalance(),
        ));

        return Command::SUCCESS;
    }

    private function resolveSliceDate(mixed $dateArgument, SymfonyStyle $io): ?DateTimeImmutable
    {
        if ($dateArgument === null || '' === trim((string) $dateArgument)) {
            return (new DateTimeImmutable())->setTime(0, 0);
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $dateArgument);
        if ($date === false) {
            $io->error(sprintf('Некорректная дата: %s. Ожидается формат Y-m-d.', (string) $dateArgument));

            return null;
        }

        return $date;
    }

    private function resolveCentralBankKeyRate(mixed $rateArgument, SymfonyStyle $io): float|false|null
    {
        if ($rateArgument === null || '' === trim((string) $rateArgument)) {
            return null;
        }

        $normalized = str_replace(',', '.', trim((string) $rateArgument));
        if (!is_numeric($normalized)) {
            $io->error(sprintf('Некорректная ключевая ставка ЦБ: %s. Ожидается число.', (string) $rateArgument));

            return false;
        }

        $rate = (float) $normalized;
        if (0 > $rate || 100 < $rate) {
            $io->error(sprintf('Некорректная ключевая ставка ЦБ: %s. Ожидается диапазон 0..100.', (string) $rateArgument));

            return false;
        }

        return $rate;
    }

    private function resolveDepositRate(mixed $rateArgument, SymfonyStyle $io): float|false
    {
        $normalized = str_replace(',', '.', trim((string) $rateArgument));
        if (!is_numeric($normalized)) {
            $io->error(sprintf('Некорректная ставка по депозиту: %s. Ожидается число.', (string) $rateArgument));

            return false;
        }

        $rate = (float) $normalized;
        if (0 > $rate || 100 < $rate) {
            $io->error(sprintf('Некорректная ставка по депозиту: %s. Ожидается диапазон 0..100.', (string) $rateArgument));

            return false;
        }

        return $rate;
    }
}
