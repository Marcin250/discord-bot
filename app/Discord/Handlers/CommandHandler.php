<?php

namespace App\Discord\Handlers;

use App\Enums\Command;
use App\ExternalApi\ChuckNorrisJokesApiClient;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Exception;
use Illuminate\Support\Str;

class CommandHandler extends AbstractHandler
{
    private const COMMANDS = [
        Command::LIST => 'listCommands',
        Command::JOKE => 'replyWithJoke',
    ];

    /** @var ChuckNorrisJokesApiClient */
    private $chuckNorrisJokesApiClient;

    public function __construct(Discord $discord)
    {
        parent::__construct($discord);
        $this->chuckNorrisJokesApiClient = new ChuckNorrisJokesApiClient();
    }

    public function handle(Message $message): void
    {
        if (!$this->isCommand($message)) {
            return;
        }

        $method = self::COMMANDS[$message->content];

        $this->{$method}($message);
    }

    private function isCommand(Message $message): bool
    {
        return Str::startsWith($message->content, '!')
            && array_key_exists($message->content, self::COMMANDS);
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
}
