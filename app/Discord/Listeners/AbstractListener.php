<?php

namespace App\Discord\Listeners;

use App\Discord\Handlers\HandlerFactory;

abstract class AbstractListener
{
    public const LISTEN_METHOD = 'listen';

    /** @var HandlerFactory */
    protected $handlerFactory;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }
}
