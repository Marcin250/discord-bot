<?php

namespace App\Discord\Handlers;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

class ModerationHandler extends AbstractHandler
{
    private const MODERATION_CASES = [
        'test123' => ['delete'],
    ];

    public function moderateMessage(Message $message): bool
    {
        $skipNextActions = [];

        foreach (self::MODERATION_CASES as $case => $methods) {
            if (!Str::contains($message->content, $case)) {
                continue;
            }

            $skipNextActions = array_merge($skipNextActions, array_map(function (string $method) use ($message, $case) {
                return $this->{$method}($message, ['case' => $case]);
            }, $methods));
        }

        return in_array(true, $skipNextActions, true);
    }

    private function delete(Message $message): bool
    {
        $message->delete();

        return true;
    }
}
