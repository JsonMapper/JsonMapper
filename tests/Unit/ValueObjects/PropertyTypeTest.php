<?php

declare(strict_types=1);

namespace JsonMapper\Tests\ValueObjects\Unit;

use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\PropertyType;
use PHPUnit\Framework\TestCase;

class PropertyTypeTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\PropertyType
     */
    public function testGettersReturnConstructorValues(): void
    {
        $isArray = ArrayInformation::notAnArray();
        $propertyType = new PropertyType('int', $isArray);

        self::assertSame('int', $propertyType->getType());
        self::assertEquals($isArray, $propertyType->getArrayInformation());
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyType
     */
    public function testCanBeConvertedToJson(): void
    {
        $propertyType = new PropertyType('int', ArrayInformation::notAnArray());

        $propertyAsJson = json_encode($propertyType);

        self::assertIsString($propertyAsJson);
        self::assertJsonStringEqualsJsonString(
            '{"type":"int","isArray":false,"arrayInformation":{"isArray":false,"dimensions":0}}',
            (string) $propertyAsJson
        );
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyType
     * @dataProvider isArrayValueAndExpectation
     */
    public function testIsArrayReturnsTCorrectForPossibleValues(ArrayInformation $isArray, bool $expected): void
    {
        $propertyType = new PropertyType('int', $isArray);

        self::assertSame($expected, $propertyType->isArray());
    }

    public function isArrayValueAndExpectation(): array
    {
        return [
            'no' => [ArrayInformation::notAnArray(), false],
            'single dimensional' => [ArrayInformation::singleDimension(), true],
            'multi dimensional' => [ArrayInformation::multiDimension(2), true,]
        ];
    }
}
