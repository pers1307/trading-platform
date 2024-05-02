<?php

namespace App\Service;

use App\Dto\Notification;
use App\Dto\OpenTradeNotifications;
use App\Entity\Stock;
use App\Entity\Trade;

class OpenTradeService
{
    const TAKE_PROFIT = 'profit';
    const STOP_LOSS = 'loss';

    /**
     * @param Trade[] $trades
     */
    public function check(array $trades): OpenTradeNotifications
    {
        $notifications = [];
        $closeWarningTrades = [];
        foreach ($trades as $trade) {
            if (
                !$this->isStockPriceValid($trade->getStock())
                || $trade->getStatus() === Trade::STATUS_CLOSE
                || $trade->isExistsTradeCloseWarning()
            ) {
                continue;
            }

            if ($this->isLoss($trade)) {
                $notifications[] = $this->createNotification($trade, self::STOP_LOSS);
                $closeWarningTrades[] = $trade;
                continue;
            }

            if ($this->isProfit($trade)) {
                $notifications[] = $this->createNotification($trade, self::TAKE_PROFIT);
                $closeWarningTrades[] = $trade;
            }
        }

        return new OpenTradeNotifications($notifications, $closeWarningTrades);
    }

    private function isStockPriceValid(Stock $stock): bool
    {
        return !empty($stock->getPrice())
            && !empty($stock->getOpen())
            && !empty($stock->getLow())
            && !empty($stock->getHigh());
    }

    private function isProfit(Trade $trade): bool
    {
        $isProfitForLongTrade =
            !empty($trade->getTakeProfit())
            && $trade->getType() == Trade::TYPE_LONG
            && (
                $trade->getStock()->getPrice() > $trade->getTakeProfit()
                || $trade->getStock()->getHigh() > $trade->getTakeProfit()
            );

        $isProfitForShortTrade =
            !empty($trade->getTakeProfit())
            && $trade->getType() == Trade::TYPE_SHORT
            && (
                $trade->getStock()->getPrice() < $trade->getTakeProfit()
                || $trade->getStock()->getLow() < $trade->getTakeProfit()
            );

        return $isProfitForLongTrade || $isProfitForShortTrade;
    }

    private function isLoss(Trade $trade): bool
    {
        $isLossForLongTrade =
            !empty($trade->getStopLoss())
            && $trade->getType() == Trade::TYPE_LONG
            && (
                $trade->getStock()->getPrice() < $trade->getStopLoss()
                || $trade->getStock()->getLow() < $trade->getStopLoss()
            );

        $isLossForShortTrade =
            !empty($trade->getStopLoss())
            && $trade->getType() == Trade::TYPE_SHORT
            && (
                $trade->getStock()->getPrice() > $trade->getStopLoss()
                || $trade->getStock()->getHigh() > $trade->getStopLoss()
            );

        return $isLossForLongTrade || $isLossForShortTrade;
    }

    private function createNotification(Trade $trade, string $tradeResult): Notification
    {
        $type = ucfirst($trade->getType());
        return new Notification(
            $tradeResult === self::TAKE_PROFIT ? "Позиция закрылась по take-profit" : "Позиция закрылась по stop-loss",
            "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type"
        );
    }
}
