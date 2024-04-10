<?php

namespace App\Service;

use App\Entity\Trade;
use App\Event\NotificationEvent;
use App\Repository\TradeRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OpenTradeService
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @todo: написать интеграционный тест
     * Для логики пишем юнит тесты
     */
    public function check(): void
    {
        $trades = $this->tradeRepository->findAllActive();

        foreach ($trades as $trade) {
            if ($trade->getType() == Trade::TYPE_LONG) {
                if (!is_null($trade->getTakeProfit())) {
                    if (
                        $trade->getStock()->getPrice() > $trade->getTakeProfit()
                        || $trade->getStock()->getHigh() > $trade->getTakeProfit()
                    ) {
                        $type = ucfirst($trade->getType());
                        $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type";
                        // todo: добавить текущую цену инструмента ???
                        $notificationEvent = new NotificationEvent(
                            "Позиция закрылась по тейк профиту",
                            $text,
                        );
                        $this->eventDispatcher->dispatch($notificationEvent);
                    }
                }
                if (!is_null($trade->getStopLoss())) {
                    if (
                        $trade->getStock()->getPrice() < $trade->getStopLoss()
                        || $trade->getStock()->getLow() < $trade->getStopLoss()
                    ) {
                        $type = ucfirst($trade->getType());
                        $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type";
                        // todo: добавить текущую цену инструмента ???
                        $notificationEvent = new NotificationEvent(
                            "Позиция закрылась по стоп лоссу",
                            $text,
                        );
                        $this->eventDispatcher->dispatch($notificationEvent);
                    }
                }
            }
            if ($trade->getType() == Trade::TYPE_SHORT) {
                if (!is_null($trade->getTakeProfit())) {
                    if (
                        $trade->getStock()->getPrice() < $trade->getTakeProfit()
                        || $trade->getStock()->getLow() < $trade->getTakeProfit()
                    ) {
                        $type = ucfirst($trade->getType());
                        $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type";
                        // todo: добавить текущую цену инструмента ???
                        $notificationEvent = new NotificationEvent(
                            "Позиция закрылась по тейк профиту",
                            $text,
                        );
                        $this->eventDispatcher->dispatch($notificationEvent);
                    }
                }
                if (!is_null($trade->getStopLoss())) {
                    if (
                        $trade->getStock()->getPrice() > $trade->getStopLoss()
                        || $trade->getStock()->getHigh() > $trade->getStopLoss()
                    ) {
                        $type = ucfirst($trade->getType());
                        $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type";
                        // todo: добавить текущую цену инструмента ???
                        $notificationEvent = new NotificationEvent(
                            "Позиция закрылась по стоп лоссу",
                            $text,
                        );
                        $this->eventDispatcher->dispatch($notificationEvent);
                    }
                }
            }
        }
    }
}
