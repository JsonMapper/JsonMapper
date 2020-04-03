<?php declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Handler;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperInterface;
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
        $mapper = new PropertyMapper();
        $json = (object) ['file' => __FILE__];
        $object = new \stdClass();

        $mapper->__invoke($json, new ObjectWrapper($object), new PropertyMap(), $this->createMock(JsonMapperInterface::class));

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
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($fileProperty);
        $json = (object) ['file' => __FILE__];
        $object = new \stdClass();
        $mapper = new PropertyMapper();

        $mapper->__invoke($json, new ObjectWrapper($object), $propertyMap, $this->createMock(JsonMapperInterface::class));

        self::assertEquals(__FILE__, $object->file);
    }

    /**
     * @covers \JsonMapper\Handler\PropertyMapper
     */
    public function testPublicBuiltinClassIsSet(): void
    {
        $fileProperty = PropertyBuilder::new()
            ->setName('createdAt')
            ->setType(\DateTimeImmutable::class)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $now = new \DateTimeImmutable();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($fileProperty);
        $json = (object) ['createdAt' => $now->format('Y-m-d\TH:i:s.uP')];
        $object = new \stdClass();
        $mapper = new PropertyMapper();

        $mapper->__invoke($json, new ObjectWrapper($object), $propertyMap, $this->createMock(JsonMapperInterface::class));

        self::assertEquals($now, $object->createdAt);
    }

}
