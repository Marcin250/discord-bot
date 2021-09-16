<?php

namespace App\ExternalApi;

use App\DTO\ChuckNorrisJoke;
use GuzzleHttp\Client;

class ChuckNorrisJokesApiClient
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function findRandomJoke(): ChuckNorrisJoke
    {
        $basePath = (string) config('external_api.chuck_norris_api.base_path');
        $endpoint = (string) config('external_api.chuck_norris_api.random_jokes_endpoint');

        $response = $this->client->get("{$basePath}/{$endpoint}")->getBody()->getContents();
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return ChuckNorrisJoke::fromArray($data);
    }
}
