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

        $this->handlerFactory->commandHandler()->handle($message);
        $this->handlerFactory->reactionHandler()->reactToMessage($message);
    }
}
