<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\ValueMapper;
use JsonMapper\Tests\Implementation\Popo;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;
use stdClass;

class ValueMapperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\ValueMapper
     * @dataProvider valueMapperDataProvider
     */
    public function testCanConvertObject(
        ValueMapper $middleware,
        stdClass $json,
        stdClass $expected
    ): void {
        $middleware->handle($json, new ObjectWrapper(new Popo()), new PropertyMap(), $this->createMock(JsonMapperInterface::class));

        self::assertEquals($expected, $json);
    }

    public function valueMapperDataProvider(): array
    {
        return [
            'php function strtoupper' => [
                new ValueMapper('strtoupper'),
                (object) [
                    'name' => 'test',
                    'notes' => 'this is a test'
                ],
                (object) [
                    'name' => 'TEST',
                    'notes' => 'THIS IS A TEST'
                ]
            ],
            'custom function' => [
                new ValueMapper(
                    static function ($key, $value) {
                        if ($key === 'notes') {
                            return \base64_decode($value);
                        }

                        return $value;
                    },
                    true
                ),
                (object) [
                    'name' => 'test',
                    'notes' => 'c3RyaW5nIGluIGJhc2U2NA=='
                ],
                (object) [
                    'name' => 'test',
                    'notes' => 'string in base64'
                ]
            ]
        ];
    }
}
