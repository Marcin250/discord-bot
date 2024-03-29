<?php

declare(strict_types=1);

namespace App\Providers;

use App\Cache\CacheFactory;
use App\Cache\CacheFactoryInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /** @var array */
    public $bindings = [
        CacheFactoryInterface::class => CacheFactory::class,
    ];

    public function provides(): array
    {
        return [
            CacheFactoryInterface::class,
        ];
    }
}
