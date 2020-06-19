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

    public function __construct(
        string $name,
        PropertyType $type,
        Visibility $visibility
    ) {
        $this->name = $name;
        $this->propertyType = $type;
        $this->visibility = $visibility;
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

    public function asBuilder(): PropertyBuilder
    {
        return PropertyBuilder::new()
            ->setName($this->name)
            ->setType($this->propertyType->getType())
            ->setIsNullable($this->propertyType->isNullable())
            ->setVisibility($this->visibility)
            ->setIsArray($this->propertyType->isArray());
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->propertyType,
            'visibility' => $this->visibility,
        ];
    }

    public function getType(): string
    {
        return $this->propertyType->getType();
    }

    public function isNullable(): bool
    {
        return $this->propertyType->isNullable();
    }

    public function isArray(): bool
    {
        return $this->propertyType->isArray();
    }
}
