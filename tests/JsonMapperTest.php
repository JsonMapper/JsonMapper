<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper;

use DannyVanDerSluijs\JsonMapper\JsonMapper;
use DannyVanDerSluijs\JsonMapper\Strategies\Reflection;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Php74\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    /**
     * @requires PHP >= 7.4
     */
    public function testItCanMapAnPhp74Object(): void
    {
        // Arrange
        $mapper = new JsonMapper([new Reflection()]);
        $object = new SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }
}
