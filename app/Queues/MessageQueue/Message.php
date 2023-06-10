<?php

declare(strict_types=1);

namespace App\Queues\MessageQueue;

class Message
{
    public function __construct(public readonly string $content)
    {
    }
}
