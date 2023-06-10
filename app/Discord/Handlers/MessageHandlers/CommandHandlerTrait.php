<?php

declare(strict_types=1);

namespace App\Discord\Handlers\MessageHandlers;

use App\Enums\Command;
use App\ExternalApi\ChuckNorrisJokesApiClient;
use App\Queues\MessageQueue;
use App\Queues\MessageQueue\Message as MessageQueueMessage;
use App\Queues\MessageQueue\Queue;
use App\Youtube\VideoDownloader;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Role;
use Discord\Parts\User\Member;
use Exception;
use Illuminate\Support\Str;
use Throwable;

trait CommandHandlerTrait
{
    use AdminCommandHandlerTrait;

    private ChuckNorrisJokesApiClient $chuckNorrisJokesApiClient;

    private VideoDownloader $videoDownloader;

    private MessageQueue $messageQueue;

    public function executeCommand(Message $message): void
    {
        if (!Str::startsWith($message->content, '!')) {
            return;
        }

        [$firstPhrase] = explode(' ', $message->content);

        $command = Command::tryFrom($firstPhrase);

        if ((!($command instanceof Command)) || ($command->isAdmin() && !$this->isAdmin($message->member))) {
            return;
        }

        if (!is_null($commandMethod = $this->commandMethod($command))) {
            $this->{$commandMethod}($message);
        }
    }

    private function isAdmin(?Member $member): bool
    {
        return $member instanceof Member
            ? ($member->roles->find(fn (Role $role) => $role->name === 'Owner')) instanceof Role
            : false;
    }

    private function commandMethod(Command $command): ?string
    {
        return match($command) {
            Command::LIST => 'listCommands',
            Command::JOKE => 'replyWithJoke',
            Command::DOWNLOAD_YOUTUBE_VIDEO => 'downloadYoutubeVideo',
            Command::DELETE_CHANNEL_MESSAGES => 'deleteChannelMessages',
            Command::QUEUE_MESSAGE => 'queueMessage',
            default => null,
        };
    }

    /** @throws Exception */
    private function listCommands(Message $message): void
    {
        $commandList = array_map(static fn (Command $command) => $command->value, Command::cases());
        $commandList = implode(', ', array_diff($commandList, [Command::LIST->value]));
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
        try {
            $youtubeVideo = $this->videoDownloader->download(Command::DOWNLOAD_YOUTUBE_VIDEO->contentAfterCommand($message->content));
            $message->author->sendMessage("Name: {$youtubeVideo->name()}");
            $message->author->sendMessage("Audio only: {$youtubeVideo->bestAudioOnlyUrl()}");
            $message->author->sendMessage("Video only: {$youtubeVideo->bestVideoOnlyUrl()}");
            $message->author->sendMessage("Video: {$youtubeVideo->bestVideoUrl()}");
        } catch (Throwable $exception) {
            $message->author->sendMessage("Error: {$exception->getMessage()}");
        }

        $message->delete();
    }

    private function queueMessage(Message $message): void
    {
        $this->messageQueue->dispatch(
            Queue::TWITCH_IRC_BOT_MESSAGE_QUEUE,
            new MessageQueueMessage(Command::QUEUE_MESSAGE->contentAfterCommand($message->content))
        );
    }
}
