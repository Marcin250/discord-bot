<?php

namespace App\DTO;

class DiscordUser
{
    /** @var string */
    private $username;

    /** @var string */
    private $discriminator;

    public function __construct(string $username, string $discriminator)
    {
        $this->username = $username;
        $this->discriminator = $discriminator;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }
}
