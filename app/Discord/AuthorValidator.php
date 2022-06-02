<?php

namespace App\Discord;

use Discord\Parts\User\Member;
use Discord\Parts\User\User;

class AuthorValidator
{
    public function isMember($author): bool
    {
        return $author instanceof Member;
    }

    public function isUser($author): bool
    {
        return $author instanceof User;
    }

    public function isBot($author): bool
    {
        return $this->isMember($author) && (($author->user instanceof User) && ($author->user->bot === true));
    }
}
