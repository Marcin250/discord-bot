<?php

declare(strict_types=1);

namespace App\Enums;

enum Command: string
{
    public function isAdmin(): bool
    {
        return in_array(
            $this,
            [
                self::DELETE_CHANNEL_MESSAGES,
            ],
            true
        );
    }

    public function contentAfterCommand(string $content): string
    {
        return trim(str_replace($this->value, '', $content));
    }

    case LIST = '!commands';

    case JOKE = '!joke';

    case DOWNLOAD_YOUTUBE_VIDEO = '!downloadYT';

    case DELETE_CHANNEL_MESSAGES = '!deleteMessages';

    case QUEUE_MESSAGE = '!queueMesssage';
}
