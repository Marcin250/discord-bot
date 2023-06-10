<?php

declare(strict_types=1);

namespace App\Queues;

use App\Queues\MessageQueue\Message;
use App\Queues\MessageQueue\Queue;
use Illuminate\Support\Facades\Redis;

class MessageQueue
{
    public function __construct()
    {
    }

    public function dispatch(Queue $queue, Message $message): void
    {
        Redis::publish($queue->value, $message->content);
    }
}
