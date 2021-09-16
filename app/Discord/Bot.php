<?php

namespace App\Discord;

use App\Builders\UnicodeStringBuilder;
use App\Discord\Listeners\AbstractListener;
use App\Discord\Listeners\ListenerFactory;
use Discord\Discord;
use Discord\WebSockets\Event;

class Bot implements BotInterface
{
    private const EVENTS = [
        Event::MESSAGE_CREATE,
        Event::MESSAGE_REACTION_ADD,
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
            $type = UnicodeStringBuilder::createFromString(mb_strtolower($event))->camel()->toString();
            $this->discord->on($event, [$this->listenerFactory->{"{$type}Listener"}(), AbstractListener::LISTEN_METHOD]);
        }

        $this->discord->run();
    }
}
