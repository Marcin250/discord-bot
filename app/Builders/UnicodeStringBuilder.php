<?php

namespace App\Builders;

use Symfony\Component\String\UnicodeString;

class UnicodeStringBuilder
{
    public static function createFromString(string $text): UnicodeString
    {
        return new UnicodeString($text);
    }
}
