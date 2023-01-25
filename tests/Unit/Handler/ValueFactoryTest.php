<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Handler;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Cache\NullCache;
use JsonMapper\Enums\Visibility;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\Handler\ValueFactory;
use JsonMapper\Helpers\ScalarCaster;
use JsonMapper\JsonMapperFactory;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\DocBlockAnnotations;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\Tests\Implementation\Models\IShape;
use JsonMapper\Tests\Implementation\Models\ShapeInstanceFactory;
use JsonMapper\Tests\Implementation\Models\Square;
use JsonMapper\Tests\Implementation\Models\User;
use JsonMapper\Tests\Implementation\Models\UserWithConstructor;
use JsonMapper\Tests\Implementation\Models\Wrappers\IShapeWrapper;
use JsonMapper\Tests\Implementation\Php81\BlogPost;
use JsonMapper\Tests\Implementation\Popo;
use JsonMapper\Tests\Implementation\PrivatePropertyWithoutSetter;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\Tests\Implementation\UserWithConstructorParent;
use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class ValueFactoryTest extends TestCase
{
    /**
     * @covers \JsonMapper\Handler\ValueFactory
     */
    public function testItCanMapToAnEmptyArrayForUnionProperty(): void
    {
        $property = PropertyBuilder::new()
            ->setName('value')
            ->addType('int', ArrayInformation::singleDimension())
            ->addType('string', ArrayInformation::singleDimension())
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $valueFactory = new ValueFactory(new ScalarCaster(), new FactoryRegistry(), new FactoryRegistry());

        $buildValue = $valueFactory->build($jsonMapper, $property, []);

        self::assertEquals([], $buildValue);
    }

    /**
     * @covers \JsonMapper\Handler\ValueFactory
     * @dataProvider combinationsOfScalarValueDataTypeAndArrayInformation
     * @param mixed $value
     */
    public function testItCanMapPropertyWithoutTypeInfo(string $type, $value): void
    {
        $property = PropertyBuilder::new()
            ->setName('value')
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $valueFactory = new ValueFactory(new ScalarCaster(), new FactoryRegistry(), new FactoryRegistry());

        $buildValue = $valueFactory->build($jsonMapper, $property, $value);

        self::assertEquals($value, $buildValue);
    }

    /**
     * @covers \JsonMapper\Handler\ValueFactory
     * @dataProvider combinationsOfScalarValueDataTypeAndArrayInformation
     * @param mixed $value
     */
    public function testItCanMapScalarPropertyWithSingleType(string $type, $value, ArrayInformation $arrayInformation): void
    {
        $property = PropertyBuilder::new()
            ->setName('value')
            ->addType($type, $arrayInformation)
            ->setIsNullable(false)
            ->setVisibility(Visibility::PUBLIC())
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);
        $valueFactory = new ValueFactory(new ScalarCaster(), new FactoryRegistry(), new FactoryRegistry());

        $buildValue = $valueFactory->build($jsonMapper, $property, $value);

        self::assertEquals($value, $buildValue);
    }

    public function scalarValueDataTypes(): array
    {
        return [
            'string' => ['string', 'Some string'],
            'boolean' => ['bool', true],
            'integer' => ['int', 1],
            'float' => ['float', M_PI],
            'double' => ['double', M_PI],
        ];
    }

    public function combinationsOfScalarValueDataTypeAndArrayInformation(): array
    {
        $values = [];
        foreach ($this->scalarValueDataTypes() as $key => [$type, $value]) {
            $values[$key . ' as single type'] = [$type, $value, ArrayInformation::notAnArray()];
            $values[$key . ' as single dimension array'] = [$type, [$value, $value], ArrayInformation::singleDimension()];
            $values[$key . ' as multi dimension array'] = [$type, [[$value], [$value]], ArrayInformation::multiDimension(2)];
        }

        return $values;
    }
}
