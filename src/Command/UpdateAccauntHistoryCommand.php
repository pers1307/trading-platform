<?php

namespace App\Command;

use App\Entity\AccauntHistory;
use App\Repository\AccauntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @todo зарефакторить и покрыть тестами
 */
#[AsCommand(
    name: 'app:update-accaunt-history',
    description: 'Обновить состояние счетов'
)]
class UpdateAccauntHistoryCommand extends Command
{
    /**
     * @todo перенести токены в БД и сделать их независимое обновление
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $iisFinamToken,
        private readonly string $iisFinamClientId,
        private readonly string $speculativeFinamToken,
        private readonly string $speculativeFinamClientId,
        private readonly string $motherFinamToken,
        private readonly string $motherFinamClientId,
        private readonly AccauntRepository $accauntRepository,
        private readonly EntityManagerInterface $entityManager,
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
            /**
             * Получение токена
             */
            $token = $this->getJwtTokenByApiKey($this->iisFinamToken);
            $accountId = $this->getAccountIdByToken($token);

            /**
             * Обновление счета ИИС
             */
            $response = $this->httpClient->request(
                'GET',
                'https://api.finam.ru/v1/accounts/' . $accountId,
                [
                    'headers' => [
                        'Authorization' => $token,
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
            $token = $this->getJwtTokenByApiKey($this->speculativeFinamToken);
            $accountId = $this->getAccountIdByToken($token);

            /**
             * Обновление счета ИИС
             */
            $response = $this->httpClient->request(
                'GET',
                'https://api.finam.ru/v1/accounts/' . $accountId,
                [
                    'headers' => [
                        'Authorization' => $token,
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
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getJwtTokenByApiKey(string $apiKey): string
    {
        $response = $this->httpClient->request(
            'POST',
            'https://api.finam.ru/v1/sessions',
            [
                'body' => json_encode([
                    'secret' => $apiKey
                ])
            ]
        );

        $responseAsArray = $response->toArray();
        return $responseAsArray['token'];
    }

    private function getAccountIdByToken(string $token): string
    {
        $response = $this->httpClient->request(
            'POST',
            'https://api.finam.ru/v1/sessions/details',
            [
                'body' => json_encode([
                    'token' => $token
                ])
            ]
        );

        $responseAsArray = $response->toArray();
        return $responseAsArray['account_ids'][0];
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
