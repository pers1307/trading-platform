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
             * Обновление счета ИИС
             */
            $response = $this->httpClient->request(
                'GET',
                'https://trade-api.finam.ru/public/api/v1/portfolio',
                [
                    'headers' => ['X-Api-Key' => $this->iisFinamToken],
                    'query' => ['clientId' => urlencode($this->iisFinamClientId)]
                ]
            );
            $iisFinamPortfolio = $response->toArray();
            $iisFinamEquity = floatval($iisFinamPortfolio['data']['equity']);
            $this->save(1, $iisFinamEquity);

            /**
             * Обновление Спекулятивного счета
             */
            $response = $this->httpClient->request(
                'GET',
                'https://trade-api.finam.ru/public/api/v1/portfolio',
                [
                    'headers' => ['X-Api-Key' => $this->speculativeFinamToken],
                    'query' => ['clientId' => urlencode($this->speculativeFinamClientId)]
                ]
            );
            $speculativeFinamPortfolio = $response->toArray();
            $speculativeFinamEquity = floatval($speculativeFinamPortfolio['data']['equity']);
            $this->save(2, $speculativeFinamEquity);

            /**
             * Обновление Маминого ИИС
             */
            $response = $this->httpClient->request(
                'GET',
                'https://trade-api.finam.ru/public/api/v1/portfolio',
                [
                    'headers' => ['X-Api-Key' => $this->motherFinamToken],
                    'query' => ['clientId' => urlencode($this->motherFinamClientId)]
                ]
            );
            $speculativeFinamPortfolio = $response->toArray();
            $motherFinamEquity = floatval($speculativeFinamPortfolio['data']['equity']);

            $this->save(3, $motherFinamEquity);
        } catch (\Throwable $exception) {
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
