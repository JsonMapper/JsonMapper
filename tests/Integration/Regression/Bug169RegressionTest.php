<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration\Regression;

use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\Tests\Implementation\Popo;
use PHPUnit\Framework\TestCase;

class Bug169RegressionTest extends TestCase
{
    /**
     * @test
     * @coversNothing
     * @requires PHP >= 8.0
     */
    public function canHandleVarNotationOnPublicProperty()
    {
        $factoryRegistry = new FactoryRegistry();
        $mapper = JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withTypedPropertiesMiddleware()
            ->withNamespaceResolverMiddleware()
            ->withObjectConstructorMiddleware($factoryRegistry)
            ->build();

        $target = new class([]) {
            public function __construct(
                /** @var Popo[] $popo */
                public array $popo
            )
            {
                // Intentionally left empty.
            }
        };
        $json = json_encode((object)['popo' => [
            (object) ['name' => 'John Doe'],
        ]]);

        $result = $mapper->mapToClassFromString($json, get_class($target));

        self::assertInstanceOf(get_class($target), $result);
        self::assertArrayHasKey(0, $result->popo);
        self::assertInstanceOf(Popo::class, $result->popo[0]);
        self::assertSame('John Doe', $result->popo[0]->name);
    }
}

