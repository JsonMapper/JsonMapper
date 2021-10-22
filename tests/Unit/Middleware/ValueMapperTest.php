<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\ValueMapper;
use JsonMapper\Tests\Implementation\Popo;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class ValueMapperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\ValueMapper
     * @dataProvider valueMapperDataProvider
     */
    public function testCanConvertObject(
        array $arguments,
        array $jsonObject,
        array $resultObject
    ): void {
        $middleware = new ValueMapper(...$arguments);

        $json = (object) $jsonObject;

        $object = new ObjectWrapper(new Popo());

        $middleware->handle($json, $object, new PropertyMap(), $this->createMock(JsonMapperInterface::class));

        self::assertObjectHasAttribute('name', $json);
        self::assertObjectHasAttribute('notes', $json);
        self::assertEquals($resultObject['notes'], $json->notes);
        self::assertEquals($resultObject['name'], $json->name);
    }

    public function valueMapperDataProvider(): array
    {
        return [
            'php function strtoupper' => [
                [
                    'strtoupper',
                ],
                [
                    'name' => 'test',
                    'notes' => 'this is a test'
                ],
                [
                    'name' => 'TEST',
                    'notes' => 'THIS IS A TEST'
                ]
            ],
            'custom function' => [
                [
                    static function ($key, $value) {
                        if ($key === 'notes') {
                            return \base64_decode($value);
                        }

                        return $value;
                    },
                    true
                ],
                [
                    'name' => 'test',
                    'notes' => 'c3RyaW5nIGluIGJhc2U2NA=='
                ],
                [
                    'name' => 'test',
                    'notes' => 'string in base64'
                ]
            ]
        ];
    }
}
