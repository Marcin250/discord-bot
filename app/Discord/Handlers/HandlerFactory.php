<?php

namespace App\Discord\Handlers;

use App\Discord\AbstractFactory;
use App\Exceptions\InvalidInstanceException;
use Discord\Discord;

/**
 * @method MessageHandler messageHandler()
 * @method ReactionHandler reactionHandler()
 */
class HandlerFactory extends AbstractFactory
{
    /** @var Discord */
    private $discord;

    public function __construct(Discord $discord)
    {
        $this->namespace = __NAMESPACE__;
        $this->instanceType = AbstractHandler::class;
        $this->discord = $discord;
    }

    protected function createInstance(string $class): object
    {
        return new $class($this->discord);
    }
}
