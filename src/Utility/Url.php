<?php

namespace UnknownRori\Router\Utility;

class Url
{
    public static function splitUrl(string $url): array
    {
        // $result = preg_split("@(?=/)@", $url);
        $result = explode("/", $url);

        array_shift($result);

        return $result;
    }
}
