<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /** @var array */
    public $bindings = [
        MessageQueue::class => MessageQueue::class,
    ];

    public function provides(): array
    {
        return [
            MessageQueue::class,
        ];
    }
}
