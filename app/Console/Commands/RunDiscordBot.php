<?php

namespace App\Console\Commands;

use App\Discord\Bot;
use Illuminate\Console\Command;

class RunDiscordBot extends Command
{
    /** @var string */
    protected $signature = 'app:run-discord-bot';

    /** @var string */
    protected $description = 'Runs discord bot';

    public function handle(): void
    {
        (new Bot())->handle();
    }
}
