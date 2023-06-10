<?php

declare(strict_types=1);

namespace App\Queues\MessageQueue;

enum Queue: string
{
    case TWITCH_IRC_BOT_MESSAGE_QUEUE = 'twitch-irc-bot:message_queue';
}
