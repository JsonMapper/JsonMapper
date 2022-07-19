<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration;

use JsonMapper\JsonMapperFactory;
use JsonMapper\Tests\Implementation\Php81\BlogPost;
use JsonMapper\Tests\Implementation\Php81\Status;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class FeatureSupportsEnumsTest extends TestCase
{
    /**
     * @requires PHP >= 8.1
     */
    public function testItCanMapAnEnumType(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new BlogPost();
        $json = (object) ['status' => 'draft'];

        $mapper->mapObject($json, $object);

        self::assertSame(Status::DRAFT, $object->status);
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testItCanMapAnToAnArrayOfEnumType(): void
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new class {
            /** @var Status[] */
            public $states;
        };
        $json = (object) ['states' => ['draft', 'archived']];

        $mapper->mapObject($json, $object);

        self::assertSame([Status::DRAFT, Status::ARCHIVED], $object->states);
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testItCanMapAnToAnMultiDimensionalArrayOfEnumType(): void
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new class {
            /** @var Status[][] */
            public $states;
        };
        $json = (object) ['states' => [['draft', 'archived'], ['published']]];

        $mapper->mapObject($json, $object);

        self::assertSame([[Status::DRAFT, Status::ARCHIVED], [Status::PUBLISHED]], $object->states);
    }
}
