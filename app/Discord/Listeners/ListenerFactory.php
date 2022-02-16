<?php

declare(strict_types=1);

namespace App\Discord\Listeners;

use App\Discord\AbstractFactory;
use App\Discord\Handlers\HandlerFactory;

/**
 * @method MessageCreateListener messageCreateListener()
 * @method MessageReactionAddListener messageReactionAddListener()
 * @method MessageReactionRemoveListener messageReactionRemoveListener()
 */
class ListenerFactory extends AbstractFactory
{
    /** @var HandlerFactory */
    private $handlerFactory;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->namespace = __NAMESPACE__;
        $this->instanceType = AbstractListener::class;
        $this->handlerFactory = $handlerFactory;
    }

    protected function createInstance(string $class): object
    {
        return new $class($this->handlerFactory);
    }
}
