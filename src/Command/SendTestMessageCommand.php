<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TelegramBot\Api\BotApi;

#[AsCommand(
    name: 'app:send-test-message',
    description: 'Отправить тестовое сообщение в телеграмм бота'
)]
class SendTestMessageCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bot = new BotApi('');

        $bot->sendMessage(199777117, 'Helloy, Juri!');

        return Command::SUCCESS;
    }
}
