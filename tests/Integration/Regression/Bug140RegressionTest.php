<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration\Regression;

use JsonMapper\Cache\NullCache;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;
use JsonMapper\Middleware;

class Bug140RegressionTest extends TestCase
{
    /**
     * @test
     * @coversNothing
     */
    public function namespaceResolvingIsAbleToResolveWhenUsingPartialUseCombinedWithNestedNamespaceInPHPdoc(): void
    {
        $class = new class {
            /** @var Middleware\Attributes\MapFrom */
            public $maps;
        };

        $json = (object) ['maps' => (object) ['source' => 'the moon']];
        $wrapper = new ObjectWrapper($class);
        $propertyMap = new ValueObjects\PropertyMap();
        $mapper = $this->createMock(JsonMapperInterface::class);
        $docBlockMiddleware = new Middleware\DocBlockAnnotations(new NullCache());
        $docBlockMiddleware->handle($json, $wrapper, $propertyMap, $mapper);

        $sut = new Middleware\NamespaceResolver(new NullCache());
        $sut->handle($json, $wrapper, $propertyMap, $mapper);

        self::assertEquals(
            [
                new ValueObjects\PropertyType(
                    Middleware\Attributes\MapFrom::class,
                    ValueObjects\ArrayInformation::notAnArray()
                )
            ],
            $propertyMap->getProperty('maps')->getPropertyTypes()
        );
    }
}
