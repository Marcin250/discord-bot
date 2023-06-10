<?php

declare(strict_types=1);

namespace App\Discord\Handlers;

use App\Cache\CacheFactoryInterface;
use App\Discord\Handlers\MessageHandlers\CommandHandlerTrait;
use App\Discord\Handlers\MessageHandlers\ModerationHandlerTrait;
use App\ExternalApi\ChuckNorrisJokesApiClient;
use App\Queues\MessageQueue;
use App\Youtube\VideoDownloader;
use Discord\Discord;
use Discord\Parts\Channel\Message;

class MessageHandler extends AbstractHandler
{
    use CommandHandlerTrait;
    use ModerationHandlerTrait;

    /** @var CacheFactoryInterface */
    protected $cacheFactory;

    public function __construct(Discord $discord)
    {
        parent::__construct($discord);

        $this->cacheFactory = app(CacheFactoryInterface::class);
        $this->chuckNorrisJokesApiClient = new ChuckNorrisJokesApiClient();
        $this->videoDownloader = new VideoDownloader();
        $this->messageQueue = new MessageQueue();
    }

    public function handleMessageCreate(Message $message): void
    {
        $this->cacheFactory->messageCacheService()->store($message);

        if ($this->moderateMessage($message)) {
            return;
        }

        $this->executeCommand($message);
    }
}
