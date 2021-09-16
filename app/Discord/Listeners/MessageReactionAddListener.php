<?php

namespace App\Discord\Listeners;

use App\Enums\Emoji;
use Discord\Parts\WebSockets\MessageReaction;

class MessageReactionAddListener extends AbstractListener
{
    public function listen(MessageReaction $reaction): void
    {
        $reactionString = $this->handlerFactory->reactionHandler()
            ->toReactionString($reaction->emoji->name, $reaction->emoji->id);

        $reactionString === Emoji::ARJEN ?
            $this->handlerFactory->reactionHandler()->reactWithArjenToArjenReaction($reaction->message)
            : null;
    }
}
