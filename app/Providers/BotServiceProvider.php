<?php

declare(strict_types=1);

namespace App\Providers;

use App\Discord\BotService;
use App\Discord\BotServiceInterface;
use App\Discord\Handlers\HandlerFactory;
use App\Discord\Listeners\ListenerFactory;
use Discord\Discord;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;

class BotServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(BotServiceInterface::class, function () {
            $discord = new Discord([
                'token' => (string) config('discord.bot.token'),
                'loop' => Loop::get(),
                'logger' => new NullLogger(),
            ]);

            return new BotService($discord, new ListenerFactory(new HandlerFactory($discord)));
        });
    }

    public function provides(): array
    {
        return [
            BotServiceInterface::class,
        ];
    }
}
