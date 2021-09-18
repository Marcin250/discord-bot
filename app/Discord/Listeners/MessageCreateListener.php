<?php

namespace App\Discord\Listeners;

use Discord\Parts\Channel\Message;

class MessageCreateListener extends AbstractListener
{
    public function listen(Message $message): void
    {
        if ($this->handlerFactory->moderationHandler()->moderateMessage($message)) {
            return;
        }

        $this->handlerFactory->commandHandler()->handleMessageCreate($message);
        $this->handlerFactory->reactionHandler()->handleMessageCreate($message);
    }
}
