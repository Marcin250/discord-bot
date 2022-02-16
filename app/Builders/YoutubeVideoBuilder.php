<?php

declare(strict_types=1);

namespace App\Builders;

use App\DTO\YoutubeVideo;

class YoutubeVideoBuilder
{
    public static function build(?string $name, ?string $bestAudioOnlyUrl, ?string $bestVideoOnlyUrl, ?string $bestVideoUrl): YoutubeVideo
    {
        return new YoutubeVideo($name, $bestAudioOnlyUrl, $bestVideoOnlyUrl, $bestVideoUrl);
    }
}
