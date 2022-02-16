<?php

declare(strict_types=1);

namespace App\DTO;

class YoutubeVideo
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $bestAudioOnlyUrl;

    /** @var string|null */
    private $bestVideoOnlyUrl;

    /** @var string|null */
    private $bestVideoUrl;

    public function __construct($name, $bestAudioOnlyUrl, $bestVideoOnlyUrl, $bestVideoUrl)
    {
        $this->name = $name;
        $this->bestAudioOnlyUrl = $bestAudioOnlyUrl;
        $this->bestVideoOnlyUrl = $bestVideoOnlyUrl;
        $this->bestVideoUrl = $bestVideoUrl;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function bestAudioOnlyUrl(): ?string
    {
        return $this->bestAudioOnlyUrl;
    }

    public function bestVideoOnlyUrl(): ?string
    {
        return $this->bestVideoOnlyUrl;
    }

    public function bestVideoUrl(): ?string
    {
        return $this->bestVideoUrl;
    }
}
