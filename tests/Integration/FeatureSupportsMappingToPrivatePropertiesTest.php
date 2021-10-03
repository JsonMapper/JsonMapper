<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration;

use JsonMapper\JsonMapperFactory;
use JsonMapper\Middleware\IgnorePropertiesVisibility;
use JsonMapper\Tests\Implementation\PopoPrivate;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * @coversNothing
 */
class FeatureSupportsMappingToPrivatePropertiesTest extends TestCase
{
    public function testItCanMapAnObjectWithPrivateProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $mapper->push(new IgnorePropertiesVisibility());
        $object = new PopoPrivate();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        $reflectedObject = new ReflectionObject($object);
        $reflectedProperty = $reflectedObject->getProperty('name');
        $reflectedProperty->setAccessible(true);
        self::assertSame(__METHOD__, $reflectedProperty->getValue($object));
    }
}
