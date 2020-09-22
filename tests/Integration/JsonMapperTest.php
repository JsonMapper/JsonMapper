<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration;

use JsonMapper\JsonMapperFactory;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\Tests\Implementation\Popo;
use JsonMapper\Tests\Implementation\Php74;
use JsonMapper\Tests\Implementation\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    public function testItCanMapAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Popo();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    public function testItCanMapAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getName());
    }

    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new SimpleObject();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->getName());
    }

    public function testItCanMapAnDateTimeImmutableProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Popo();
        $json = (object) ['date' => '2020-03-08 12:42:14'];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertEquals(new \DateTimeImmutable('2020-03-08 12:42:14'), $object->date);
    }

    /**
     * @requires PHP >= 7.4
     */
    public function testItCanMapAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Php74\Popo();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    /**
     * @requires PHP >= 7.4
     */
    public function testItAppliesTypeCastingMappingAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Php74\Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    /**
     * @requires PHP >= 7.4
     */
    public function testItHandlesPropertyTypedAsArray(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Php74\Popo();
        $json = (object) ['friends' => [__METHOD__, __CLASS__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame([__METHOD__, __CLASS__], $object->friends);
    }

    public function testItHandlesPropertyDocumentedAsArrayProvidedAsObject(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Popo();
        $json = (object) ['notes' => (object) ['one' => __METHOD__, 'two' => __CLASS__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(['one' => __METHOD__, 'two' => __CLASS__], $object->notes);
    }

    public function testItCanMapAnObjectWithACustomClassAttribute(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new ComplexObject();
        $json = (object) ['child' => (object) ['name' => __METHOD__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getChild()->getName());
    }

    public function testItCanMapAnObjectWithACustomClassAttributeFromAnotherNamespace(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new ComplexObject();
        $json = (object) ['user' => (object) ['name' => __METHOD__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getUser()->getName());
    }

    public function testItCanMapAnObjectWithAnArrayOfScalarValues(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new ComplexObject();
        $one = new SimpleObject();
        $one->setName('ONE');
        $two = new SimpleObject();
        $two->setName('TWO');
        $json = (object) ['children' => [(object) ['name' => 'ONE'], (object) ['name' => 'TWO']]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertIsArray($object->getChildren());
        self::assertContainsOnly(SimpleObject::class, $object->getChildren());
        self::assertEquals([$one, $two], $object->getChildren());
    }

	public function testItCanMapAnObjectFromString(): void
	{
		// Arrange
		$mapper = (new JsonMapperFactory())->bestFit();
		$object = new Popo();
		$json =  '{"name": "one"}';

		// Act
		$mapper->mapObjectFromString($json, $object);

		// Assert
		self::assertSame('one', $object->name);
	}

	public function testItCanLaunchExceptionOnInvalidJson(): void
	{
		// Arrange
		$mapper = (new JsonMapperFactory())->bestFit();
		$object = new Popo();
		$jsonString =  '{"name": one}';

		$this->expectException(\JsonException::class);

		// Act
		$mapper->mapObjectFromString($jsonString, $object);
	}

    public function testItCanMapAnArrayOfObjects(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new SimpleObject();
        $json = [(object) ['name' => 'one'], (object) ['name' => 'two']];

        // Act
        $result = $mapper->mapArray($json, $object);

        // Assert
        self::assertContainsOnly(SimpleObject::class, $result);
        self::assertSame('one', $result[0]->getName());
        self::assertSame('two', $result[1]->getName());
    }

	public function testItCanMapAnArrayOfString(): void
	{
		// Arrange
		$mapper = (new JsonMapperFactory())->bestFit();
		$object = new SimpleObject();
		$json = '[{"name": "one"}, {"name": "two"}]';

		// Act
		$result = $mapper->mapArrayFromString($json, $object);

		// Assert
		self::assertContainsOnly(SimpleObject::class, $result);
		self::assertSame('one', $result[0]->getName());
		self::assertSame('two', $result[1]->getName());
	}

    /**
     * @dataProvider scalarValueDataTypes
     */
    public function testItSetsTheValueAsIsForMixedType($value): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new ComplexObject();
        $json = (object) ['mixedParam' => $value];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame($value, $object->mixedParam);
    }

    /**
     * @requires PHP >= 7.4
     */
    public function testItMapsClassFromTheSameNamespace(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Php74\PopoWrapper();
        $json = (object) ['wrappee' => (object) ['name' => 'two']];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertNotNull($object->wrappee);
        self::assertSame('two', $object->wrappee->name);
    }

    public function scalarValueDataTypes(): array
    {
        return [
            'string' => ['Some string'],
            'boolean' => [true],
            'integer' => [1],
            'float' => [M_PI],
        ];
    }
}
