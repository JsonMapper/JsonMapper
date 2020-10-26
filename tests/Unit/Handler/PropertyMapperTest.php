<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Handler;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\Exception\ClassFactoryException;
use JsonMapper\Handler\ClassFactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\Tests\Implementation\Models\UserWithConstructor;
use JsonMapper\Tests\Implementation\Popo;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\Tests\Implementation\UserWithConstructorParent;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class PropertyMapperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testAdditionalJsonIsIgnored(): void
    {
        $propertyMapper = new PropertyMapper();
        $json = (object) ['file' => __FILE__];
        $object = new \stdClass();
        $wrapped = new ObjectWrapper($object);

        $propertyMapper->__invoke($json, $wrapped, new PropertyMap(), $this->createMock(JsonMapperInterface::class));

        self::assertEquals(new \stdClass(), $object);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicScalarValueIsSet(): void
    {
        $fileProperty = PropertyBuilder::new()
            ->setName('file')
            ->setType('string')
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($fileProperty);
        $json = (object) ['file' => __FILE__];
        $object = new \stdClass();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $this->createMock(JsonMapperInterface::class));

        self::assertEquals(__FILE__, $object->file);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicBuiltinClassIsSet(): void
    {
        $property = PropertyBuilder::new()
            ->setName('createdAt')
            ->setType(\DateTimeImmutable::class)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->setIsArray(false)
            ->build();
        $now = new \DateTimeImmutable();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $json = (object) ['createdAt' => $now->format('Y-m-d\TH:i:s.uP')];
        $object = new \stdClass();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $this->createMock(JsonMapperInterface::class));

        self::assertEquals($now, $object->createdAt);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicCustomClassIsSet(): void
    {
        $property = PropertyBuilder::new()
            ->setName('child')
            ->setType(SimpleObject::class)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $jsonMapper->expects(self::once())
            ->method('mapObject')
            ->with((object) ['name' => __FUNCTION__], self::isInstanceOf(SimpleObject::class))
            ->willReturnCallback(static function (\stdClass $json, SimpleObject $object) {
                $object->setName($json->name);
            });
        $json = (object) ['child' => (object) ['name' => __FUNCTION__]];
        $object = new ComplexObject();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $jsonMapper);

        self::assertEquals(__FUNCTION__, $object->getChild()->getName());
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicScalarValueArrayIsSet(): void
    {
        $fileProperty = PropertyBuilder::new()
            ->setName('ids')
            ->setType('int')
            ->setIsArray(true)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($fileProperty);
        $json = (object) ['ids' => [1, 2, 3]];
        $object = new \stdClass();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $this->createMock(JsonMapperInterface::class));

        self::assertEquals([1, 2, 3], $object->ids);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicCustomClassArrayIsSet(): void
    {
        $property = PropertyBuilder::new()
            ->setName('children')
            ->setType(SimpleObject::class)
            ->setIsArray(true)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PRIVATE())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $jsonMapper->expects(self::exactly(2))
            ->method('mapObject')
            ->with((object) ['name' => __FUNCTION__], self::isInstanceOf(SimpleObject::class))
            ->willReturnCallback(static function (\stdClass $json, SimpleObject $object) {
                $object->setName($json->name);
            });
        $json = (object) ['children' => [(object) ['name' => __FUNCTION__], (object) ['name' => __FUNCTION__]]];
        $object = new ComplexObject();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $jsonMapper);

        self::assertEquals(2, count($object->getChildren()));
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testArrayPropertyIsCasted(): void
    {
        $property = PropertyBuilder::new()
            ->setName('notes')
            ->setType('string')
            ->setIsArray(true)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $json = (object) ['notes' => (object) ['note_one' => __FUNCTION__, 'note_two' => __CLASS__]];
        $object = new Popo();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $jsonMapper);

        self::assertEquals(['note_one' => __FUNCTION__, 'note_two' => __CLASS__], $object->notes);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testCanMapPropertyWithClassFactory(): void
    {
        $property = PropertyBuilder::new()
            ->setName('user')
            ->setType(UserWithConstructor::class)
            ->setIsArray(false)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $json = (object) ['user' => (object) ['id' => 1234, 'name' => 'John Doe']];
        $object = new UserWithConstructorParent();
        $wrapped = new ObjectWrapper($object);
        $classFactoryRegistry = new ClassFactoryRegistry();
        $classFactoryRegistry->loadNativePhpClassFactories();
        $classFactoryRegistry->addFactory(
            UserWithConstructor::class,
            static function ($params) {
                return new UserWithConstructor($params->id, $params->name);
            }
        );
        $propertyMapper = new PropertyMapper($classFactoryRegistry);

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $jsonMapper);

        self::assertEquals(new UserWithConstructor(1234, 'John Doe'), $object->user);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicNullableCustomClassNullIsNotSet(): void
    {
        $property = PropertyBuilder::new()
            ->setName('child')
            ->setType(SimpleObject::class)
            ->setIsNullable(true)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $json = (object) ['child' => null];
        $object = new ComplexObject();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();

        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $jsonMapper);

        self::assertNull($object->getChild());
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicNotNullableCustomClassThrowsException(): void
    {
        $property = PropertyBuilder::new()
            ->setName('child')
            ->setType(SimpleObject::class)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $json = (object) ['child' => null];
        $object = new ComplexObject();
        $wrapped = new ObjectWrapper($object);
        $propertyMapper = new PropertyMapper();
		self::expectException(\Throwable::class);
        $propertyMapper->__invoke($json, $wrapped, $propertyMap, $jsonMapper);
    }
}
