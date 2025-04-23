<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\ValueObjects;


use JsonMapper\Enums\Visibility;
use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\LazyAnnotationMap;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyType;
use PHPUnit\Framework\TestCase;

class LazyAnnotationMapTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     */
    public function testHasVarReturnsTrueWhenInputHasVarTag(): void
    {
        $map = new LazyAnnotationMap('/** @var string */');

        $this->assertTrue($map->hasVar());
    }

    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     */
    public function testHasVarReturnsFalseWhenInputHasNoVarTag(): void
    {
        $map = new LazyAnnotationMap('/** @param string $test */');

        $this->assertFalse($map->hasVar());
    }

    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     */
    public function testHasParamReturnsTrueWhenInputHasParamTag(): void
    {
        $map = new LazyAnnotationMap('/** @param string $test */');

        $this->assertTrue($map->hasParam('test'));
    }

    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     */
    public function testHasParamReturnsFalseWhenInputHasNoParamTag(): void
    {
        $map = new LazyAnnotationMap('/** @var string */');

        $this->assertFalse($map->hasParam('test'));
    }

    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     */
    public function testThrowsForUnknownTagWhenCallingTagToPropertyBuilder(): void
    {
        $map = new LazyAnnotationMap('/**  */');

        $this->expectException(\RuntimeException::class);
        $map->tagToPropertyBuilder('var');
    }

    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     */
    public function testThrowsForTagWithoutTypeWhenCallingTagToPropertyBuilder(): void
    {
        $map = new LazyAnnotationMap('/** @deprecated */');

        $this->expectException(\RuntimeException::class);
        $map->tagToPropertyBuilder('deprecated');
    }

    /**
     * @covers \JsonMapper\ValueObjects\LazyAnnotationMap
     * @dataProvider parseDataProvider
     */
    public function testCorrectlyParsesInput(string $input, string $tagName, ?string $variableName, Property $expected): void
    {
        $map = new LazyAnnotationMap($input);

        $property = $map->tagToPropertyBuilder($tagName, $variableName)
            ->setName('')
            ->setVisibility(Visibility::PUBLIC())
            ->build();

        self::assertEquals($expected, $property);
    }

    public function parseDataProvider(): \Generator
    {
        yield 'Simple string' => [
            'input' => '/** @var string */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), false, new PropertyType('string', ArrayInformation::notAnArray()))
        ];
        yield 'Simple integer' => [
            'input' => '/** @var int */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), false, new PropertyType('int', ArrayInformation::notAnArray()))
        ];
        yield 'Array of strings' => [
            'input' => '/** @var array<int, string> */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), false, new PropertyType('string', ArrayInformation::singleDimension()))
        ];
        yield 'Nullable string' => [
            'input' => '/** @var ?string */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), true, new PropertyType('string', ArrayInformation::notAnArray()))
        ];
        yield 'true pseudo type' => [
            'input' => '/** @var ?true */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), true, new PropertyType('bool', ArrayInformation::notAnArray()))
        ];
        yield 'Simple int string union' => [
            'input' => '/** @var string|int */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), false, new PropertyType('string', ArrayInformation::notAnArray()), new PropertyType('int', ArrayInformation::notAnArray()))
        ];
        yield 'Simple int null union' => [
            'input' => '/** @var int|null */',
            'tagName' => 'var',
            'variableName' => null,
            new Property('', Visibility::PUBLIC(), true, new PropertyType('int', ArrayInformation::notAnArray()))
        ];
    }
}
