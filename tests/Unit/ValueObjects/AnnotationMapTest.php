<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\ValueObjects;

use JsonMapper\ValueObjects\AnnotationMap;
use PHPUnit\Framework\TestCase;

class AnnotationMapTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testGettersReturnConstructorValues(): void
    {
        $params = ['name' => 'string', 'age' => 'int'];
        $annotationMap = new AnnotationMap('varName', $params, 'int');

        self::assertSame('varName', $annotationMap->getVar());
        self::assertSame($params, $annotationMap->getParams());
        self::assertSame('int', $annotationMap->getReturn());
    }

    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testGettersReturnConstructorValuesWithDefaults(): void
    {
        $annotationMap = new AnnotationMap();

        self::assertFalse($annotationMap->hasVar());
        self::assertEmpty($annotationMap->getParams());
        self::assertFalse($annotationMap->hasReturn());
    }

    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testGetVarThrowsErrorWhenNoVarAvailable(): void
    {
        $annotationMap = new AnnotationMap();

        self::assertFalse($annotationMap->hasVar());
        $this->expectException(\Exception::class);
        $annotationMap->getVar();
    }

    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testGetReturnThrowsErrorWhenNoReturnAvailable(): void
    {
        $annotationMap = new AnnotationMap();

        self::assertFalse($annotationMap->hasReturn());
        $this->expectException(\Exception::class);
        $annotationMap->getReturn();
    }

    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testHasParamReturnsCorrectValues(): void
    {
        $params = ['name' => 'string', 'age' => 'int'];
        $annotationMap = new AnnotationMap('varName', $params, 'int');

        self::assertTrue($annotationMap->hasParam('name'));
        self::assertTrue($annotationMap->hasParam('age'));
        self::assertFalse($annotationMap->hasParam('email'));
    }

    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testGetParamReturnsParamIfAvailable(): void
    {
        $params = ['name' => 'string', 'age' => 'int'];
        $annotationMap = new AnnotationMap('varName', $params, 'int');

        self::assertEquals($params['name'], $annotationMap->getParam('name'));
        self::assertEquals($params['age'], $annotationMap->getParam('age'));
    }

    /**
     * @covers \JsonMapper\ValueObjects\AnnotationMap
     */
    public function testGetParamThrowsExceptionWhenParamNotAvaiable(): void
    {
        $params = ['name' => 'string', 'age' => 'int'];
        $annotationMap = new AnnotationMap('varName', $params, 'int');

        $this->expectException(\Exception::class);
        $annotationMap->getParam('email');
    }
}
