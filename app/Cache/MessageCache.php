<?php

namespace App\Cache;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Facades\Cache;

class MessageCache
{
    private const USER_CHANNEL_MESSAGE_PATTERN = 'ch%s_u%s_m%s';

    public static function store(Message $message): void
    {
        Cache::forever(
            self::generateKeyForUserChannelMessage($message->channel_id, $message->user_id, $message->id),
            (string) $message
        );
    }

    private static function generateKeyForUserChannelMessage(string $channelId, string $userId, string $messageId) : string
    {
        return sprintf(self::USER_CHANNEL_MESSAGE_PATTERN, $channelId, $userId, $messageId);
    }
}
