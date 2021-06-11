<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

trait CommandTrait
{
    protected static $commands = [
        '!joke' => 'sendJoke'
    ];

    /** @var Discord */
    private $discord;

    protected function isCommand(Message $message): bool
    {
        return Str::startsWith($message->content, '!');
    }

    protected function executeCommand(Message $message): void
    {
        Assert::keyExists(static::$commands, $message->content);

        $method = static::$commands[$message->content];

        $this->{$method}($message);
    }

    /** @throws GuzzleException|Exception */
    private function sendJoke(Message $message): void
    {
        $response = (new Client())->get('https://api.chucknorris.io/jokes/random')->getBody()->getContents();
        $joke = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        $message->reply($joke['value']);
    }
}
