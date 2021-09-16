<?php

namespace App\Discord\Handlers;

use App\Enums\Emoji;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

class ReactionHandler extends AbstractHandler
{
    private const REACTIONS = [
        'hello' => [Emoji::WAVE],
        'super' => [Emoji::PEPE_YEA, Emoji::THUMBS_UP],
        'forever' => [Emoji::ARJEN, Emoji::FLOOR],
    ];

    public function reactToMessage(Message $message): void
    {
        if ($message->author->username === $this->discord->user->username) {
            return;
        }

        foreach (self::REACTIONS as $phrase => $reactions) {
            if (Str::contains(Str::lower($message->content), $phrase)) {
                array_walk($reactions, static function (string $emoji) use ($message) {
                    $message->react($emoji);
                });
            }
        }
    }

    public function reactWithArjenToArjenReaction(Message $message): void
    {
        $message->react(Emoji::ARJEN);
    }

    public function toReactionString(?string $name, ?string $id): string
    {
        return ":{$name}:{$id}";
    }
}
