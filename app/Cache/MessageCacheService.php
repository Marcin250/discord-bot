<?php

declare(strict_types=1);

namespace App\Cache;

use App\Discord\AuthorValidator;
use Discord\Parts\Channel\Message;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use Webmozart\Assert\Assert;

class MessageCacheService implements CacheServiceInterface
{
    private const USERS_CHANNEL_MESSAGE_KEY_PATTERN = 'ChannelsMessages:%s:Users:%s';
    private const BOTS_CHANNEL_MESSAGE_KEY_PATTERN = 'ChannelsMessages:%s:Bots:%s';

    /** @var Connection */
    private $connection;

    /** @var AuthorValidator */
    private $authorValidator;

    public function __construct()
    {
        $this->connection = Redis::connection();
        $this->authorValidator = new AuthorValidator();
    }

    public function store(Message $message): void
    {
        $this->connection->client()->rPush($this->key($message), (string) $message);
    }

    private function key(Message $message): string
    {
        return sprintf(
            $this->authorValidator->isBot($message->author)
                ? self::BOTS_CHANNEL_MESSAGE_KEY_PATTERN
                : self::USERS_CHANNEL_MESSAGE_KEY_PATTERN,
            $message->channel_id,
            $message->user_id
        );
    }
}
