<?php

namespace JsonMapper\Cache;

use Psr\SimpleCache\CacheInterface;

class ArrayCache implements CacheInterface
{
    private $cache = [];

    public function get($key, $default = null)
    {
        self::ensureKeyArgumentIsValidSingleKey($key);

        return $this->cache[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        self::ensureKeyArgumentIsValidSingleKey($key);

        $this->cache[$key] = $value;
    }

    public function delete($key)
    {
        self::ensureKeyArgumentIsValidSingleKey($key);

        unset($this->cache[$key]);
    }

    public function clear()
    {
        $this->cache = [];
    }

    public function getMultiple($keys, $default = null)
    {
        self::ensureKeyArgumentIsValidSetOfKeys($keys);

        $values = [];
        array_walk($keys, function($key) use ($default, &$values) {
            $values[$key] = $this->cache[$key] ?? $default;
        });

        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        self::ensureKeyArgumentIsValidSetOfKeys(array_keys($values));

        $this->cache = array_merge($this->cache, $values);
    }

    public function deleteMultiple($keys)
    {
        self::ensureKeyArgumentIsValidSetOfKeys($keys);

        array_walk($keys, function ($key) { unset($this->cache[$key]); });
    }

    public function has($key)
    {
        self::ensureKeyArgumentIsValidSingleKey($key);

        return array_key_exists($key, $this->cache);
    }

    private static function ensureKeyArgumentIsValidSingleKey($key): void
    {
        if (is_string($key)) {
            return;
        }

        throw InvalidArgumentException::forCacheKey($key);
    }

    private static function ensureKeyArgumentIsValidSetOfKeys($keys): void
    {
        if (is_array($keys) || $keys instanceof \Traversable) {

            array_walk($keys, function ($key) {
                self::ensureKeyArgumentIsValidSingleKey($key);
            });
            return;
        }

        throw InvalidArgumentException::forCacheKey($keys);
    }
}