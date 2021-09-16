<?php

namespace App\Providers;

use App\Discord\Bot;
use App\Discord\BotInterface;
use App\Discord\Handlers\HandlerFactory;
use App\Discord\Listeners\ListenerFactory;
use Discord\Discord;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Factory;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BotInterface::class, function () {
            $discord = new Discord([
                'token' => (string) config('discord.bot.token'),
                'loop' => Factory::create(),
            ]);

            return new Bot($discord, new ListenerFactory(new HandlerFactory($discord)));
        });
    }

    public function provides(): array
    {
        return [
            BotInterface::class,
        ];
    }
}
