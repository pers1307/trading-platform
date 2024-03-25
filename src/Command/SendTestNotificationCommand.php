<?php

namespace App\Command;

use App\Event\NotificationEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'app:send-test-notification',
    description: 'Отправить тестовое тестовую нотификацию'
)]
class SendTestNotificationCommand extends Command
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @todo покрыть тестами, наверно только интеграционными
         */
        $notificationEvent = new NotificationEvent('Привет!', 'Helloy, Juri!');
        $this->eventDispatcher->dispatch($notificationEvent);

        return Command::SUCCESS;
    }
}
