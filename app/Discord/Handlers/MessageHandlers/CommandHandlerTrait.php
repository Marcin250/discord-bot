<?php

namespace App\Discord\Handlers\MessageHandlers;

use App\Builders\DiscordAdminBuilder;
use App\Enums\Command;
use App\ExternalApi\ChuckNorrisJokesApiClient;
use App\Youtube\VideoDownloader;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use Exception;
use Illuminate\Support\Str;
use Throwable;

trait CommandHandlerTrait
{
    use AdminCommandHandlerTrait;

    protected static $commands = [
        Command::LIST => 'listCommands',
        Command::JOKE => 'replyWithJoke',
        Command::DOWNLOAD_YOUTUBE_VIDEO => 'downloadYoutubeVideo',
    ];

    protected static $adminCommands = [
        Command::DELETE_CHANNEL_MESSAGES => 'deleteChannelMessages',
    ];

    /** @var ChuckNorrisJokesApiClient */
    private $chuckNorrisJokesApiClient;

    /** @var VideoDownloader */
    private $videoDownloader;

    public function __construct(Discord $discord)
    {
        parent::__construct($discord);
        $this->chuckNorrisJokesApiClient = new ChuckNorrisJokesApiClient();
        $this->videoDownloader = new VideoDownloader();
    }

    public function executeCommand(Message $message): void
    {
        if (!Str::startsWith($message->content, '!')) {
            return;
        }

        [$firstPhrase] = explode(' ', $message->content);

        if (array_key_exists($firstPhrase, static::$adminCommands) && $this->isAdmin($message->author)) {
            $this->{static::$adminCommands[$firstPhrase]}($message);

            return;
        }

        if (array_key_exists($firstPhrase, static::$commands)) {
            $this->{static::$commands[$firstPhrase]}($message);
        }
    }

    private function isAdmin(User $user): bool
    {
        $discordAdmin = DiscordAdminBuilder::fromConfig();

        return $user->username === $discordAdmin->getUsername()
            && $user->discriminator === $discordAdmin->getDiscriminator();
    }

    /** @throws Exception */
    private function listCommands(Message $message): void
    {
        $commandList = implode(', ', array_diff(array_keys(static::$commands), [Command::LIST]));
        $this->discord->getChannel($message->channel_id)->sendMessage("Available commands: {$commandList}");
    }

    /** @throws Exception */
    private function replyWithJoke(Message $message): void
    {
        $message->reply($this->chuckNorrisJokesApiClient->findRandomJoke()->value());
    }

    /** @throws Exception */
    private function downloadYoutubeVideo(Message $message): void
    {
        $videoUrl = trim(str_replace(Command::DOWNLOAD_YOUTUBE_VIDEO, '', $message->content));

        try {
            $youtubeVideo = $this->videoDownloader->download($videoUrl);
            $message->author->sendMessage("Name: {$youtubeVideo->name()}");
            $message->author->sendMessage("Audio only: {$youtubeVideo->bestAudioOnlyUrl()}");
            $message->author->sendMessage("Video only: {$youtubeVideo->bestVideoOnlyUrl()}");
            $message->author->sendMessage("Video: {$youtubeVideo->bestVideoUrl()}");
        } catch (Throwable $exception) {
            $message->author->sendMessage("Blad: {$exception->getMessage()}");
        }

        $message->delete();
    }
}
