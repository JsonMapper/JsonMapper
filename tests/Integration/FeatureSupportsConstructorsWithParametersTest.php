<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration;

use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\Tests\Implementation\Php81\WithConstructorPropertyPromotion;
use JsonMapper\Tests\Implementation\PopoWrapperWithConstructor;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class FeatureSupportsConstructorsWithParametersTest extends TestCase
{
    public function testCanHandleCustomConstructors(): void
    {
        $factoryRegistry = new FactoryRegistry();
        $mapper = JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withObjectConstructorMiddleware($factoryRegistry)
            ->withPropertyMapper(new PropertyMapper($factoryRegistry))
            ->build();

        $json = (object) [
            'popo' => (object) [
                'name' => 'John Doe'
            ]
        ];

        $result = $mapper->mapToClass($json, PopoWrapperWithConstructor::class);

        self::assertInstanceOf(PopoWrapperWithConstructor::class, $result);
        self::assertEquals($json->popo->name, $result->getPopo()->name);
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testCanHandleCustomConstructorsWithPropertyPromotion(): void
    {
        $factoryRegistry = new FactoryRegistry();
        $mapper = JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withObjectConstructorMiddleware($factoryRegistry)
            ->withPropertyMapper(new PropertyMapper($factoryRegistry))
            ->build();

        $json = (object) [
            'name' => 'John Doe',
        ];

        $result = $mapper->mapToClass($json, WithConstructorPropertyPromotion::class);

        self::assertInstanceOf(WithConstructorPropertyPromotion::class, $result);
        self::assertEquals($json->name, $result->name);
    }

}