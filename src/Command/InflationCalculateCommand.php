<?php

namespace App\Command;

use App\Entity\AccauntInflation;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntRepository;
use App\Service\AccauntInflation\AccauntInflationCalculator;
use App\Service\AccauntInflation\AccauntInflationRequest;
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
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('movement_amount', InputArgument::REQUIRED, 'Движение средств за период')
            ->addArgument('accaunt_id', InputArgument::REQUIRED, 'Идентификатор счета')
            ->addArgument('date', InputArgument::REQUIRED, 'Дата среза в формате Y-m-d');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $movementAmount = (float) $input->getArgument('movement_amount');
        $accauntId = (int) $input->getArgument('accaunt_id');
        $dateString = (string) $input->getArgument('date');

        $accaunt = $this->accauntRepository->find($accauntId);
        if (null === $accaunt) {
            $io->error(sprintf('Счет с id=%d не найден.', $accauntId));

            return Command::FAILURE;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);
        if (false === $date) {
            $io->error(sprintf('Некорректная дата: %s. Ожидается формат Y-m-d.', $dateString));

            return Command::FAILURE;
        }

        try {
            $result = $this->accauntInflationCalculator->calculate(
                new AccauntInflationRequest($accaunt, $movementAmount, $date)
            );
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $existing = $this->accauntInflationRepository->findOneBy([
            'accaunt' => $accaunt,
            'date' => $result->getDate(),
        ]);

        if (null === $existing) {
            $existing = new AccauntInflation();
            $existing->setAccaunt($accaunt);
            $existing->setDate($result->getDate());
            $this->entityManager->persist($existing);
        }

        $existing
            ->setMovementAmount($result->getMovementAmount())
            ->setCentralBankKeyRate($result->getCentralBankKeyRate())
            ->setDepositRate($result->getDepositRate())
            ->setAccauntBalance($result->getAccauntBalance())
            ->setAccauntInflationBalance($result->getAccauntInflationBalance())
            ->setAccauntDepositBalance($result->getAccauntDepositBalance());

        $this->entityManager->flush();

        $io->success(sprintf(
            'Счет %d (%s): ставка ЦБ %0.2f%%, инфляц. баланс %0.2f, депозит %0.2f, номинал %0.2f',
            $accauntId,
            $result->getDate()->format('Y-m-d'),
            $result->getCentralBankKeyRate(),
            $result->getAccauntInflationBalance(),
            $result->getAccauntDepositBalance(),
            $result->getAccauntBalance(),
        ));

        return Command::SUCCESS;
    }
}
