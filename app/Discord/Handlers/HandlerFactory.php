<?php

namespace App\Discord\Handlers;

use App\Discord\AbstractFactory;
use App\Exceptions\InvalidInstanceException;
use Discord\Discord;

/**
 * @method CommandHandler commandHandler()
 * @method ReactionHandler reactionHandler()
 * @method ModerationHandler moderationHandler()
 */
class HandlerFactory extends AbstractFactory
{
    /** @var Discord */
    private $discord;

    public function __construct(Discord $discord)
    {
        $this->namespace = __NAMESPACE__;
        $this->discord = $discord;
    }

    protected function createInstance(string $class): object
    {
        return new $class($this->discord);
    }

    /** @throws InvalidInstanceException */
    protected function validateInstance(object $instance): void
    {
        if (!($instance instanceof AbstractHandler)) {
            throw new InvalidInstanceException('');
        }
    }
}
