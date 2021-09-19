<?php

namespace App\Discord\Handlers\MessageHandlers;

use App\Builders\DiscordAdminBuilder;
use App\Enums\Command;
use App\ExternalApi\ChuckNorrisJokesApiClient;
use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait CommandHandlerTrait
{
    protected static $commands = [
        Command::LIST => 'listCommands',
        Command::JOKE => 'replyWithJoke',
    ];

    protected static $adminCommands = [
        Command::DELETE_CHANNEL_MESSAGES => 'deleteChannelMessages',
    ];

    /** @var ChuckNorrisJokesApiClient */
    private $chuckNorrisJokesApiClient;

    public function __construct(Discord $discord)
    {
        parent::__construct($discord);
        $this->chuckNorrisJokesApiClient = new ChuckNorrisJokesApiClient();
    }

    public function executeCommand(Message $message): void
    {
        if (array_key_exists($message->content, static::$adminCommands) && $this->isAdmin($message->author)) {
            $this->{static::$adminCommands[$message->content]}($message);

            return;
        }

        if (!$this->isCommand($message)) {
            return;
        }

        $this->{static::$commands[$message->content]}($message);
    }

    private function isCommand(Message $message): bool
    {
        return Str::startsWith($message->content, '!')
            && array_key_exists($message->content, static::$commands);
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
    private function deleteChannelMessages(Message $message): void
    {
        $this->discord->getChannel($message->channel_id)->getMessageHistory([])->done(function (Collection $messages) use ($message) {
            $messageCount = count($messages);
            Log::info("Pending deletion: Channel[ID:{$message->channel_id}, Name:{$message->channel->name}, Messages: {$messageCount}]");

            /** @var Message $messageToDelete */
            foreach ($messages as $messageToDelete) {
                $messageToDelete->delete();
            }
        });
    }
}
