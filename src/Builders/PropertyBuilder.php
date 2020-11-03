<?php

declare(strict_types=1);

namespace JsonMapper\Builders;

use JsonMapper\Enums\Visibility;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyType;

class PropertyBuilder
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var bool */
    private $isNullable;
    /** @var Visibility */
    private $visibility;
    /** @var bool */
    private $isArray;

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function build(): Property
    {
        return new Property(
            $this->name,
            new PropertyType($this->type, $this->isNullable, $this->isArray),
            $this->visibility
        );
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setIsNullable(bool $isNullable): self
    {
        $this->isNullable = $isNullable;
        return $this;
    }

    public function setVisibility(Visibility $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }

    public function setIsArray(bool $isArray): self
    {
        $this->isArray = $isArray;
        return $this;
    }
}
