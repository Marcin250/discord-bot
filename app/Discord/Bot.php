<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\MessageReaction;
use Discord\WebSockets\Event;
use InvalidArgumentException;
use React\EventLoop\Factory;

class Bot
{
    use CommandTrait;
    use ReactionTrait;
    use ModerationTrait;

    /** @var Discord */
    private $discord;

    public function __construct()
    {
        $this->discord = new Discord([
            'token' => (string) config('discord.bot.token'),
            'loop' => Factory::create(),
        ]);
    }

    public function handle(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message) {
            if ($this->shouldModerate($message) && $this->moderateMessage($message)) {
                return;
            }

            $this->isCommand($message) ? $this->handleCommand($message) : null;
            $this->shouldReact($message) ? $this->reactToMessage($message) : null;
        });

        $this->discord->on(Event::MESSAGE_REACTION_ADD, function (MessageReaction $reaction) {
            $reactionString = $this->toReactionString($reaction->emoji->name, $reaction->emoji->id);

            $reactionString === Emoji::ARJEN ? $this->reactWithArjenToArjenReaction($reaction->message) : null;
        });

        $this->discord->run();
    }

    private function handleCommand(Message $message): void
    {
        try {
            $this->executeCommand($message);
        } catch (InvalidArgumentException $ex) {
            $message->reply('please provide valid command');
        }
    }
}
