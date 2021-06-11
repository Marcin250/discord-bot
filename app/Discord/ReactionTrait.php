<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

trait ReactionTrait
{
    protected static $reactions = [
        'hello' => [Emoji::WAVE],
        'super' => [Emoji::PEPE_YEA],
        'forever' => [Emoji::ARJEN, Emoji::FLOOR],
    ];

    /** @var Discord */
    private $discord;

    protected function shouldReact(Message $message): bool
    {
        if ($message->author->username === $this->discord->user->username) {
            return false;
        }

        foreach (array_keys(static::$reactions) as $phrase) {
            if (Str::contains(Str::lower($message->content), $phrase)) {
                return true;
            }
        }

        return false;
    }

    protected function reactToMessage(Message $message): void
    {
        foreach (static::$reactions as $phrase => $reactions) {
            if (Str::contains(Str::lower($message->content), $phrase)) {
                array_walk($reactions, static function (string $emoji) use ($message) {
                    $message->react($emoji);
                });
            }
        }
    }

    protected function reactWithArjenToArjenReaction(Message $message): void
    {
        $message->react(Emoji::ARJEN);
    }

    protected function toReactionString(?string $name, ?string $id): string
    {
        return ":{$name}:{$id}";
    }
}
