<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AccauntHistory;
use App\Repository\AccauntRepository;
use App\Service\Finam\Request\AuthDetailsRequest;
use App\Service\Finam\Request\AuthRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Finam\Client as FinamClient;

/**
 * @todo зарефакторить и покрыть тестами
 */
#[AsCommand(
    name: 'app:update-accaunt-history',
    description: 'Обновить состояние счетов'
)]
class UpdateAccauntHistoryCommand extends Command
{
    public function __construct(
        private readonly FinamClient $finamClient,
        private readonly HttpClientInterface $httpClient,
        private readonly string $iisFinamToken,
        private readonly string $speculativeFinamToken,
        private readonly AccauntRepository $accauntRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $historyLogger,
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
        try {
            $authResponse = $this->finamClient->authResource->getAuthToken(new AuthRequest($this->iisFinamToken));
            $authDetailsResponse = $this->finamClient->authResource->getAuthDetails(new AuthDetailsRequest($authResponse->token));

            /**
             * Обновление счета ИИС
             */
            $response = $this->httpClient->request(
                'GET',
                'https://api.finam.ru/v1/accounts/' . $authDetailsResponse->accountId,
                [
                    'headers' => [
                        'Authorization' => $authResponse->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $iisAccount = $response->toArray();
            $iisAccountEquity = floatval($iisAccount['equity']['value']);
            /**
             * @todo пригодится для оперативного отслеживания поступления средств
             */
            $iisAccountCash = floatval($iisAccount['cash'][0]['units']);
            $this->save(1, $iisAccountEquity);

            /**
             * Обновление Спекулятивного счета
             */
            $authResponse = $this->finamClient->authResource->getAuthToken(new AuthRequest($this->speculativeFinamToken));
            $authDetailsResponse = $this->finamClient->authResource->getAuthDetails(new AuthDetailsRequest($authResponse->token));

            /**
             * Обновление счета Вклада
             */
            $response = $this->httpClient->request(
                'GET',
                'https://api.finam.ru/v1/accounts/' . $authDetailsResponse->accountId,
                [
                    'headers' => [
                        'Authorization' => $authResponse->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $speculativeAccount = $response->toArray();
            $speculativeAccountEquity = floatval($speculativeAccount['equity']['value']);
            /**
             * @todo пригодится для оперативного отслеживания поступления средств
             */
            $speculativeAccountCash = floatval($speculativeAccount['cash'][0]['units']);
            $this->save(2, $speculativeAccountEquity);
        } catch (\Throwable $exception) {
            $this->historyLogger->error($exception);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function save(int $accauntId, float $value): void
    {
        $accaunt = $this->accauntRepository->find($accauntId);

        $accauntHistory = new AccauntHistory();
        $accauntHistory->setAccaunt($accaunt);
        $accauntHistory->setBalance($value);

        $this->entityManager->persist($accauntHistory);
        $this->entityManager->flush();
    }
}
