<?php

namespace App\Discord\Handlers;

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

class CommandHandler extends AbstractHandler
{
    private const COMMANDS = [
        Command::LIST => 'listCommands',
        Command::JOKE => 'replyWithJoke',
    ];

    private const ADMIN_COMMANDS = [
        Command::DELETE_CHANNEL_MESSAGES => 'deleteChannelMessages',
    ];

    /** @var ChuckNorrisJokesApiClient */
    private $chuckNorrisJokesApiClient;

    public function __construct(Discord $discord)
    {
        parent::__construct($discord);
        $this->chuckNorrisJokesApiClient = new ChuckNorrisJokesApiClient();
    }

    public function handleMessageCreate(Message $message): void
    {
        if (array_key_exists($message->content, self::ADMIN_COMMANDS) && $this->isAdmin($message->author)) {
            $this->{self::ADMIN_COMMANDS[$message->content]}($message);

            return;
        }

        if (!$this->isCommand($message)) {
            return;
        }

        $this->{self::COMMANDS[$message->content]}($message);
    }

    private function isCommand(Message $message): bool
    {
        return Str::startsWith($message->content, '!')
            && array_key_exists($message->content, self::COMMANDS);
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
        $commandList = implode(', ', array_diff(array_keys(self::COMMANDS), [Command::LIST]));
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
