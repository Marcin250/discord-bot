<?php

namespace App\Discord\Handlers;

use App\Cache\MessageCache;
use App\Discord\Handlers\MessageHandlers\CommandHandlerTrait;
use App\Discord\Handlers\MessageHandlers\ModerationHandlerTrait;
use Discord\Parts\Channel\Message;

class MessageHandler extends AbstractHandler
{
    use CommandHandlerTrait;
    use ModerationHandlerTrait;

    public function handleMessageCreate(Message $message): void
    {
        MessageCache::store($message);

        if ($this->moderateMessage($message)) {
            return;
        }

        $this->executeCommand($message);
    }
}
