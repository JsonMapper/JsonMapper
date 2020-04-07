<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Builder;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use PHPUnit\Framework\TestCase;

class PropertyBuilderTest extends TestCase
{
    /**
     * @covers \JsonMapper\Builders\PropertyBuilder
     */
    public function testCanBuildPropertyWithAllPropertiesSet(): void
    {
        $property = PropertyBuilder::new()
            ->setName('enabled')
            ->setType('boolean')
            ->setIsNullable(true)
            ->setVisibility(Visibility::PRIVATE())
            ->build();

        self::assertSame('enabled', $property->getName());
        self::assertSame('boolean', $property->getType());
        self::assertTrue($property->isNullable());
        self::assertTrue($property->getVisibility()->equals(Visibility::PRIVATE()));
    }
}
