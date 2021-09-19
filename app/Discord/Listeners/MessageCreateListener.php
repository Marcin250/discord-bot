<?php

namespace App\Discord\Listeners;

use Discord\Parts\Channel\Message;

class MessageCreateListener extends AbstractListener
{
    public function listen(Message $message): void
    {
        $this->handlerFactory->messageHandler()->handleMessageCreate($message);
        $this->handlerFactory->reactionHandler()->handleMessageCreate($message);
    }
}
