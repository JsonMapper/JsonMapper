<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;

class Property implements \JsonSerializable
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var bool */
    private $isNullable;
    /** @var Visibility */
    private $visibility;

    public function __construct(string $name, string $type, bool $isNullable, Visibility $visibility)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->visibility = $visibility;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function asBuilder(): PropertyBuilder
    {
        return PropertyBuilder::new()
            ->setName($this->name)
            ->setType($this->type)
            ->setIsNullable($this->isNullable)
            ->setVisibility($this->visibility);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'isNullable' => $this->isNullable,
            'visibility' => $this->visibility,
        ];
    }
}
