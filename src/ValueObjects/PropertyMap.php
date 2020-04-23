<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

class PropertyMap implements \IteratorAggregate, \JsonSerializable
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

    public function merge(self $other): void
    {
        foreach ($other as $property) {
            $this->addProperty($property);
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->map);
    }

    public function jsonSerialize(): array
    {
        return [
            'properties' => $this->map,
        ];
    }

    public function toString(): string
    {
        return (string) json_encode($this);
    }
}
