<?php

declare(strict_types=1);

namespace App\Enums;

class Command
{
    public const LIST = '!commands';

    public const JOKE = '!joke';

    public const DOWNLOAD_YOUTUBE_VIDEO = '!downloadYT';

    // ADMIN
    public const DELETE_CHANNEL_MESSAGES = '!deleteMessages';
}
