<?php

declare(strict_types=1);

namespace JsonMapper;

use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;

class JsonMapper implements JsonMapperInterface
{
    /** @var callable */
    private $handler;
    /** @var array */
    private $stack = [];
    /** @var callable|null */
    private $cached;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function push(callable $middleware, string $name = ''): JsonMapperInterface
    {
        $this->stack[] = [$middleware, $name];
        $this->cached = null;

        return $this;
    }

    public function pop(): JsonMapperInterface
    {
        array_pop($this->stack);
        $this->cached = null;

        return $this;
    }

    public function unshift(callable $middleware, string $name = null): JsonMapperInterface
    {
        array_unshift($this->stack, [$middleware, $name]);
        $this->cached = null;

        return $this;
    }

    public function shift(): JsonMapperInterface
    {
        array_shift($this->stack);
        $this->cached = null;

        return $this;
    }

    public function remove(callable $remove): JsonMapperInterface
    {
        $this->stack = array_values(array_filter(
            $this->stack,
            static function ($tuple) use ($remove) {
                return $tuple[0] !== $remove;
            }
        ));
        $this->cached = null;

        return $this;
    }

    public function removeByName(string $remove): JsonMapperInterface
    {
        $this->stack = array_values(array_filter(
            $this->stack,
            static function ($tuple) use ($remove) {
                return $tuple[1] !== $remove;
            }
        ));
        $this->cached = null;

        return $this;
    }

    public function mapObject(\stdClass $json, object $object): void
    {
        $propertyMap = new PropertyMap();

        $handler = $this->resolve();
        $handler($json, new ObjectWrapper($object), $propertyMap, $this);
    }

    public function mapArray(array $json, object $object): array
    {
        $results = [];
        foreach ($json as $key => $value) {
            $results[$key] = clone $object;
            $this->mapObject($value, $results[$key]);
        }

        return $results;
    }

    private function resolve(): callable
    {
        if (!$this->cached) {
            $prev = $this->handler;
            foreach (array_reverse($this->stack) as $fn) {
                $prev = $fn[0]($prev);
            }

            $this->cached = $prev;
        }

        return $this->cached;
    }
}
