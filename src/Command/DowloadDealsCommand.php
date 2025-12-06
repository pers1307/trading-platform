<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Deal;
use App\Repository\AccauntRepository;
use App\Repository\DealRepository;
use App\Repository\StockRepository;
use App\Service\Finam\Request\AuthDetailsRequest;
use App\Service\Finam\Request\AuthRequest;
use DateTime;
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
 * @todo зарефакторить + написать тесты
 * @todo Сейчас нет нужды в этом функционале, поэтому он отключен
 */
#[AsCommand(
    name: 'app:dowload-deals',
    description: 'Скачать новые сделки со счетов'
)]
class DowloadDealsCommand extends Command
{
    public function __construct (
        private readonly FinamClient $finamClient,
        private readonly HttpClientInterface $httpClient,
        private readonly string $iisFinamToken,
        private readonly string $speculativeFinamToken,
        private readonly DealRepository $dealRepository,
        private readonly StockRepository $stockRepository,
        private readonly AccauntRepository $accauntRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $dealsLogger,
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
        $authResponse = $this->finamClient->authResource->getAuthToken(new AuthRequest($this->iisFinamToken));
        $authDetailsResponse = $this->finamClient->authResource->getAuthDetails(new AuthDetailsRequest($authResponse->token));

        try {
//            $this->download($this->iisFinamToken, $authDetailsResponse->accountId, 1);
        } catch (\Throwable $exception) {
            $this->dealsLogger->error($exception);
        }

        try {
//            $this->download($this->speculativeFinamToken, $authDetailsResponse->accountId, 2);
        } catch (\Throwable $exception) {
            $this->dealsLogger->error($exception);
        }

        return Command::SUCCESS;
    }

//    private function download(string $finamToken, string $finamClientId, int $accauntId): void
//    {
//        $response = $this->httpClient->request(
//            'GET',
//            'https://trade-api.finam.ru/public/api/v1/orders',
//            [
//                'headers' => ['X-Api-Key' => $finamToken],
//                'query' => [
//                    'clientId' => urlencode($finamClientId),
//                    'includeMatched' => urlencode('true')
//                ]
//            ]
//        );
//        $orderArray = $response->toArray();
//
//        $accaunt = $this->accauntRepository->find($accauntId);
//
//        foreach ($orderArray['data']['orders'] as $order)
//        {
//            $deal = $this->dealRepository->findOneBy(['transactionId' => $order['transactionId']]);
//            if (!is_null($deal)) {
//                continue;
//            }
//
//            $deal = new Deal();
//            $deal->setPrice(floatval($order['price']));
//
//            $type = Deal::TYPE_LONG;
//            if ($order['buySell'] === 'Sell') {
//                $type = Deal::TYPE_SHORT;
//            }
//            $deal->setType($type);
//
//            $stock = $this->stockRepository->findOneBy(['secId' => $order['securityCode']]);
//            if (!is_null($stock)) {
//                $deal->setStock($stock);
//            }
//            $deal->setSecId($order['securityCode']);
//            $deal->setLots($order['quantity']);
//            $deal->setAccaunt($accaunt);
//
//            $deal->setDateTime(
//                (new DateTime($order['createdAt']))->add(new \DateInterval('PT3H'))
//            );
//            $deal->setTransactionId($order['transactionId']);
//
//            $this->entityManager->persist($deal);
//        }
//
//        $this->entityManager->flush();
//    }
}
