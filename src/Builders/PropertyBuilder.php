<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Builders;

use DannyVanDerSluijs\JsonMapper\Enums\Visibility;
use DannyVanDerSluijs\JsonMapper\ValueObjects\Property;

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

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function build(): Property
    {
        return new Property($this->name, $this->type, $this->isNullable, $this->visibility);
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
}
