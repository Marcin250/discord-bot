<?php

declare(strict_types=1);

namespace App\Builders;

use App\DTO\DiscordAdmin;

class DiscordAdminBuilder
{
    public static function fromConfig(): DiscordAdmin
    {
        [$username, $discriminator] = explode('#', config('discord.admin'));

        return new DiscordAdmin($username, $discriminator);
    }
}
