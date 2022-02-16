<?php

declare(strict_types=1);

namespace App\Discord\Handlers\MessageHandlers;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

trait ModerationHandlerTrait
{
    protected static $moderationCases = [
        'test123' => ['deleteMessage'],
    ];

    public function moderateMessage(Message $message): bool
    {
        $skipNextActions = [];

        foreach (static::$moderationCases as $case => $methods) {
            if (!Str::contains($message->content, $case)) {
                continue;
            }

            $skipNextActions = array_merge($skipNextActions, array_map(function (string $method) use ($message) {
                return $this->{$method}($message);
            }, $methods));
        }

        return in_array(true, $skipNextActions, true);
    }

    private function deleteMessage(Message $message): bool
    {
        $message->delete();

        return true;
    }
}
