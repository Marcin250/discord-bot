<?php

namespace App\Youtube;

use App\Builders\YoutubeVideoBuilder;
use App\DTO\YoutubeVideo;
use App\Exceptions\InvalidYoutubeVideoIdException;
use App\Exceptions\MissingFormatsException;
use Discord\Exceptions\FileNotFoundException;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use stdClass;

class VideoDownloader
{
    private const NODE_MODULES_YOUTUBE_DL_EXEC_BIN_YOUTUBE_DL = 'node_modules/youtube-dl-exec/bin/youtube-dl';
    private const COMMAND_PARAMETERS_PATTERN = '%s --format=18 --dump-single-json --no-warnings --no-call-home --no-check-certificate --prefer-free-formats --youtube-skip-dash-manifest --referer=%s > %s';

    /** @throws InvalidYoutubeVideoIdException|FileNotFoundException|MissingFormatsException */
    public function download(string $youtubeVideoUrl): YoutubeVideo
    {
        $query = parse_url($youtubeVideoUrl)['query'];
        parse_str($query, $parameters);
        $videoId = $parameters['v'] ?? null;

        if (empty($videoId)) {
            throw new InvalidYoutubeVideoIdException('Invalid youtube video id');
        }

        if (!file_exists($this->getExecPath())) {
            throw new FileNotFoundException("Required file: {$this->getExecPath()} is missing");
        }

        $resultFilePath = $this->generateResultFilePath();

        exec($this->createCommand($youtubeVideoUrl, $resultFilePath));

        if (!file_exists($resultFilePath)) {
            throw new FileNotFoundException("Details file not found");
        }

        $details = json_decode(file_get_contents($resultFilePath));

        unlink($resultFilePath);

        if (!isset($details->formats)) {
            throw new MissingFormatsException('Formats missing');
        }

        return YoutubeVideoBuilder::build(
            $details->title ?? 'Brak nazwy',
            $this->findBestAudioOnly($details->formats),
            $this->findBestVideoOnly($details->formats),
            $this->findBestVideo($details->formats)
        );
    }

    private function getExecPath(): string
    {
        return base_path(self::NODE_MODULES_YOUTUBE_DL_EXEC_BIN_YOUTUBE_DL);
    }

    private function createCommand(string $youtubeVideoUrl, string $resultFilePath): string
    {
        return sprintf(
            "{$this->getExecPath()} %s",
            sprintf(self::COMMAND_PARAMETERS_PATTERN, $youtubeVideoUrl, $youtubeVideoUrl, $resultFilePath)
        );
    }

    private function generateResultFilePath(): string
    {
        $uuid = Uuid::uuid4()->toString();

        return storage_path("app/{$uuid}");
    }

    /** @param stdClass[] $formats */
    private function findBestAudioOnly(array $formats): ?string
    {
        $url = null;
        $largest = 0;

        foreach ($formats as $format) {
            if (!isset($format->filesize) || !Str::contains($format->format, 'audio only')) {
                continue;
            }

            if ($format->filesize > $largest) {
                $largest = $format->filesize;
                $url = $format->url;
            }
        }

        return $url;
    }

    /** @param stdClass[] $formats */
    private function findBestVideo(array $formats): ?string
    {
        $url = null;
        $largest = 0;

        foreach ($formats as $format) {
            if (!isset($format->filesize) || !Str::contains($format->format_id, '18')) {
                continue;
            }

            if ($format->filesize > $largest) {
                $largest = $format->filesize;
                $url = $format->url;
            }
        }

        return $url;
    }

    /** @param stdClass[] $formats */
    private function findBestVideoOnly(array $formats): ?string
    {
        $url = null;
        $largest = 0;

        foreach ($formats as $format) {
            if (!isset($format->filesize) || $format->ext !== 'mp4') {
                continue;
            }

            if ($format->filesize > $largest) {
                $largest = $format->filesize;
                $url = $format->url;
            }
        }

        return $url;
    }
}
