<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Helpers;

use JsonMapper\Enums\Visibility;
use JsonMapper\ValueObjects\Property;
use PHPUnit\Framework\Assert;

class PropertyAssertionChain
{
    /** @var Property */
    private $property;

    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    public function hasName(string $name): PropertyAssertionChain
    {
        Assert::assertSame($name, $this->property->getName());

        return $this;
    }

    public function hasType(string $type): PropertyAssertionChain
    {
        Assert::assertSame($type, $this->property->getType());

        return $this;
    }

    public function hasPropertyType(string $type): PropertyAssertionChain
    {
        Assert::assertSame($type, $this->property->getPropertyType()->getType());

        return $this;
    }

    public function hasVisibility(Visibility $visibility): PropertyAssertionChain
    {
        Assert::assertTrue($this->property->getVisibility()->equals($visibility));

        return $this;
    }

    public function isNullable(): PropertyAssertionChain
    {
        Assert::assertTrue($this->property->isNullable());

        return $this;
    }

    public function isNotNullable(): PropertyAssertionChain
    {
        Assert::assertFalse($this->property->isNullable());

        return $this;
    }

    public function isArray(): PropertyAssertionChain
    {
        Assert::assertTrue($this->property->isArray());

        return $this;
    }

    public function isNotArray(): PropertyAssertionChain
    {
        Assert::assertFalse($this->property->isArray());

        return $this;
    }
}
