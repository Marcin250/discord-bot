<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

trait ModerationTrait
{
    protected static $moderationCases = [
        'test123' => ['delete'],
    ];

    /** @var Discord */
    private $discord;

    protected function shouldModerate(Message $message): bool
    {
        return $this->recognizeCase($message) !== null;
    }

    protected function moderateMessage(Message $message): bool
    {
        $skipNextActions = [];

        foreach (static::$moderationCases as $case => $methods) {
            if (!Str::contains($message->content, $case)) {
                continue;
            }

            $skipNextActions = array_merge($skipNextActions, array_map(function (string $method) use ($message, $case) {
                return $this->{$method}($message, ['case' => $case]);
            }, $methods));
        }

        return in_array(true, $skipNextActions, true);
    }

    private function recognizeCase(Message $message): ?string
    {
        foreach (array_keys(static::$moderationCases) as $case) {
            if (Str::contains($message->content, $case)) {
                return $case;
            }
        }

        return null;
    }

    private function delete(Message $message, array $arguments = []): bool
    {
        $message->delete();

        return true;
    }
}
