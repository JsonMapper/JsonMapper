<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;

class Property implements \JsonSerializable
{
    /** @var string */
    private $name;
    /** @var PropertyType */
    private $propertyType;
    /** @var Visibility */
    private $visibility;
    /** @var bool */
    private $isNullable;

    public function __construct(
        string $name,
        PropertyType $type,
        Visibility $visibility,
        bool $isNullable
    ) {
        $this->name = $name;
        $this->propertyType = $type;
        $this->visibility = $visibility;
        $this->isNullable = $isNullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPropertyType(): PropertyType
    {
        return $this->propertyType;
    }

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function asBuilder(): PropertyBuilder
    {
        return PropertyBuilder::new()
            ->setName($this->name)
            ->setType($this->propertyType->getType())
            ->setIsNullable($this->isNullable())
            ->setVisibility($this->visibility)
            ->setIsArray($this->propertyType->isArray());
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->propertyType,
            'visibility' => $this->visibility,
            'isNullable' => $this->isNullable,
        ];
    }
}
