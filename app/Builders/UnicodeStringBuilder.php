<?php

declare(strict_types=1);

namespace App\Builders;

use Symfony\Component\String\UnicodeString;

class UnicodeStringBuilder
{
    public static function createFromString(string $text): UnicodeString
    {
        return new UnicodeString($text);
    }
}
