<?php

namespace App\Fabric;

use TelegramBot\Api\BotApi;

class TelegrammBotApiFabric
{
    /**
     * @throws \Exception
     */
    public function getBotApi(string $token): BotApi
    {
        return new BotApi($token);
    }
}
