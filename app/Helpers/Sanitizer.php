<?php

namespace App\Helpers;

class Sanitizer
{
    public static function clean(string $value): string
    {
        return trim(filter_var($value, FILTER_SANITIZE_STRING));
    }
}
