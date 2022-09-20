<?php

declare(strict_types=1);

namespace JsonMapper\Middleware\Constructor;

/**
 * @psalm-immutable
 */
class Parameter
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var int */
    private $position;
    /**
     * @var mixed
     */
    private $defaultValue;

    /** @param mixed $defaultValue */
    public function __construct(string $name, string $type, int $position, $defaultValue)
    {
        $this->name = $name;
        $this->type = $type;
        $this->position = $position;
        $this->defaultValue = $defaultValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /** @return mixed */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}