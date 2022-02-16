<?php

declare(strict_types=1);

namespace App\Providers;

use App\Discord\BotService;
use App\Discord\BotServiceInterface;
use App\Discord\Handlers\HandlerFactory;
use App\Discord\Listeners\ListenerFactory;
use Discord\Discord;
use Illuminate\Support\ServiceProvider;
use Psr\Log\NullLogger;
use React\EventLoop\Factory;

class BotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BotServiceInterface::class, function () {
            $discord = new Discord([
                'token' => (string) config('discord.bot.token'),
                'loop' => Factory::create(),
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
