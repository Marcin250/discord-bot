<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Discord\BotServiceInterface;
use Illuminate\Console\Command;

class RunBotService extends Command
{
    /** @var string */
    protected $signature = 'app:run-bot-service';

    /** @var string */
    protected $description = 'Runs bot';

    public function handle(BotServiceInterface $botService): void
    {
        $botService->run();
    }
}
