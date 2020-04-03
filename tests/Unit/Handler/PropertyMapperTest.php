<?php declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Handler;

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

}
