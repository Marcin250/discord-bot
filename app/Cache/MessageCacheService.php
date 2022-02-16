<?php

declare(strict_types=1);

namespace App\Cache;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Facades\Cache;

class MessageCacheService implements CacheServiceInterface
{
    private const USER_CHANNEL_MESSAGE_KEY_PATTERN = 'ChannelUserMessage_%s_%s_%s';

    public function store(Message $message): void
    {
        Cache::forever(
            sprintf(
                self::USER_CHANNEL_MESSAGE_KEY_PATTERN,
                $message->channel_id,
                $message->user_id,
                $message->id
            ),
            (string) $message
        );
    }
}
