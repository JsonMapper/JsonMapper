<?php

namespace JsonMapper\Cache;

class InvalidArgumentException extends \InvalidArgumentException implements \Psr\SimpleCache\InvalidArgumentException
{
    private $invalidArgument;

    public static function forCacheKey($key): self
    {
        $e = new self('An invalid cache key was provided.');
        $e->invalidArgument = $key;

        return $e;
    }
}