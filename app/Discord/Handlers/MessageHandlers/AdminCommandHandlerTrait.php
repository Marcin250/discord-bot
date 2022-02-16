<?php

declare(strict_types=1);

namespace App\Discord\Handlers\MessageHandlers;

use Discord\Helpers\Collection;
use Discord\Parts\Channel\Message;
use Exception;
use Illuminate\Support\Facades\Log;

trait AdminCommandHandlerTrait
{
    /** @throws Exception */
    private function deleteChannelMessages(Message $message): void
    {
        $this->discord->getChannel($message->channel_id)->getMessageHistory([])->done(function (Collection $messages) use ($message) {
            $messageCount = count($messages);
            Log::info("Pending deletion: Channel[ID:{$message->channel_id}, Name:{$message->channel->name}, Messages: {$messageCount}]");

            /** @var Message $messageToDelete */
            foreach ($messages as $messageToDelete) {
                $messageToDelete->delete();
            }
        });
    }
}
