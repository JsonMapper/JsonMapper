<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Helpers;

use JsonMapper\Helpers\AnnotationHelper;
use PHPUnit\Framework\TestCase;

class AnnotationHelperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @dataProvider nullableAnnotations
     */
    public function testItSeesNullableFromAnnotations(string $annotation): void
    {
        self::assertTrue(AnnotationHelper::isNullable($annotation));
    }

    /**
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @dataProvider nonNullableAnnotations
     */
    public function testItSeesScalarTypesAsNotNullableFromAnnotations(string $annotation): void
    {
        self::assertFalse(AnnotationHelper::isNullable($annotation));
    }

    public function nullableAnnotations(): array
    {
        return [
            'only null' => ['null'],
            'starting with null' => ['null|int'],
            'ending with null' => ['int|null'],
            'null in between' => ['int|string|null'],
        ];
    }

    public function nonNullableAnnotations(): array
    {
        return [
            'string' => ['string'],
            'int' => ['int'],
            'bool' => ['bool'],
            'complex' => ['string|int|bool'],
        ];
    }
}
