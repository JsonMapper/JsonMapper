<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

class PropertyType implements \JsonSerializable
{
    /** @var string */
    private $type;
    /** @var bool */
    private $isNullable;
    /** @var bool */
    private $isArray;

    public function __construct(string $type, bool $isNullable, bool $isArray)
    {
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->isArray = $isArray;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'isNullable' => $this->isNullable,
            'isArray' => $this->isArray,
        ];
    }
}
