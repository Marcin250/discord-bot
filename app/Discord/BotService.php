<?php

declare(strict_types=1);

namespace App\Discord;

use App\Builders\UnicodeStringBuilder;
use App\Discord\Listeners\AbstractListener;
use App\Discord\Listeners\ListenerFactory;
use Discord\Discord;
use Discord\WebSockets\Event;
use Illuminate\Support\Facades\Log;

class BotService implements BotServiceInterface
{
    private const EVENTS = [
        Event::MESSAGE_CREATE,
        Event::MESSAGE_REACTION_ADD,
        Event::MESSAGE_REACTION_REMOVE,
    ];

    /** @var Discord */
    private $discord;

    /** @var ListenerFactory */
    private $listenerFactory;

    public function __construct(Discord $discord, ListenerFactory $listenerFactory)
    {
        $this->discord = $discord;
        $this->listenerFactory = $listenerFactory;
    }

    public function run(): void
    {
        foreach (self::EVENTS as $event) {
            $type = UnicodeStringBuilder::createFromString($event)->lower()->camel()->toString();
            $this->discord->on($event, [$this->listenerFactory->{"{$type}Listener"}(), AbstractListener::LISTEN_METHOD]);

            Log::info("[{$event}] event registered");
        }

        $this->discord->run();
    }
}
