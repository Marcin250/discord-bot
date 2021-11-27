<?php

namespace App\Discord\Handlers;

use App\Enums\Emoji;
use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\MessageReaction;
use Illuminate\Support\Str;

class ReactionHandler extends AbstractHandler
{
    private const MESSAGE_CREATE_REACTIONS = [
        'hello' => [Emoji::WAVE],
        'super' => [Emoji::PEPE_YEA, Emoji::THUMBS_UP],
        'forever' => [Emoji::ARJEN, Emoji::FLOOR],
    ];

    private const MESSAGE_REACTION_ADD_REACTIONS = [
        Emoji::ARJEN => [Emoji::ARJEN],
    ];
    private const MESSAGE_REACTION_REMOVE_REACTIONS = self::MESSAGE_REACTION_ADD_REACTIONS;

    public function handleMessageCreate(Message $message): void
    {
        if ($message->author->username === $this->discord->user->username) {
            return;
        }

        foreach (self::MESSAGE_CREATE_REACTIONS as $phrase => $reactions) {
            if (Str::contains(Str::lower($message->content), $phrase)) {
                array_walk($reactions, static function (string $emoji) use ($message) {
                    $message->react($emoji);
                });
            }
        }
    }

    public function handleMessageReactionAdd(MessageReaction $reaction): void
    {
        foreach (self::MESSAGE_REACTION_ADD_REACTIONS[$reaction->emoji->toReactionString()] ?? [] as $emoji) {
            $reaction->message->react($emoji);
        }
    }

    public function handleMessageReactionRemove(MessageReaction $reaction): void
    {
        foreach (self::MESSAGE_REACTION_REMOVE_REACTIONS[$reaction->emoji->toReactionString()] ?? [] as $emoji) {
            $reaction->message->deleteReaction(Message::REACT_DELETE_EMOJI, $emoji);
        }
    }
}
