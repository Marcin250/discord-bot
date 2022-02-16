<?php

declare(strict_types=1);

namespace App\Discord\Listeners;

use Discord\Parts\WebSockets\MessageReaction;

class MessageReactionAddListener extends AbstractListener
{
    public function listen(MessageReaction $reaction): void
    {
        $this->handlerFactory->reactionHandler()->handleMessageReactionAdd($reaction);
    }
}
