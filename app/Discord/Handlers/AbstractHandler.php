<?php

declare(strict_types=1);

namespace App\Discord\Handlers;

use Discord\Discord;

abstract class AbstractHandler
{
    /** @var Discord */
    protected $discord;

    public function __construct(Discord $discord)
    {
        $this->discord = $discord;
    }
}
