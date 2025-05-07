<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration;

use JsonMapper\JsonMapperFactory;
use JsonMapper\Tests\Implementation\Popo;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class FeatureSupportsMapToClassWithAssociativeArray extends TestCase
{
    public function testItCanMapToClass(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $json =  json_decode('{"name": "one"}', true);

        // Act
        $object = $mapper->mapToClass($json, Popo::class);

        // Assert
        self::assertSame('one', $object->name);
    }
}
