<?php

namespace App\Discord\Listeners;

use App\Discord\AbstractFactory;
use App\Discord\Handlers\HandlerFactory;
use App\Exceptions\InvalidInstanceException;

/**
 * @method MessageCreateListener messageCreateListener()
 * @method MessageReactionAddListener messageReactionAddListener()
 */
class ListenerFactory extends AbstractFactory
{
    /** @var HandlerFactory */
    private $handlerFactory;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->namespace = __NAMESPACE__;
        $this->handlerFactory = $handlerFactory;
    }

    protected function createInstance(string $class): object
    {
        return new $class($this->handlerFactory);
    }

    /** @throws InvalidInstanceException */
    protected function validateInstance(object $instance): void
    {
        if (!($instance instanceof AbstractListener)) {
            throw new InvalidInstanceException('');
        }
    }
}
