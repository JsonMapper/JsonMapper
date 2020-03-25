<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

use IteratorAggregate;

class PropertyMap implements IteratorAggregate
{
    /** @var Property[] */
    private $map = [];

    public function addProperty(Property $property): void
    {
        $this->map[$property->getName()] = $property;
    }

    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->map);
    }

    public function getProperty(string $key): Property
    {
        if (! $this->hasProperty($key)) {
            throw new \Exception("There is no property named $key");
        }

        return $this->map[$key];
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->map);
    }
}
