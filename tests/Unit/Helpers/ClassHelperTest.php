<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Helpers;

use JsonMapper\Helpers\ClassHelper;
use JsonMapper\Tests\Implementation\ComplexObject;
use PHPUnit\Framework\TestCase;

class ClassHelperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Helpers\ClassHelper
     * @dataProvider builtinClassDataProvider
     */
    public function testBuiltinClassesAreSeenAsBuiltin(string $type): void
    {
        self::assertTrue(ClassHelper::isBuiltin($type));
    }

    /**
     * @covers \JsonMapper\Helpers\ClassHelper
     */
    public function testNonExistingClassNameIsNotSeenAsBuiltinClass(): void
    {
        self::assertFalse(ClassHelper::isBuiltin('asdf'));
    }

    /**
     * @covers \JsonMapper\Helpers\ClassHelper
     * @dataProvider customClassDataProvider
     */
    public function testCustomClassesAreSeenAsCustom(string $type): void
    {
        self::assertTrue(ClassHelper::isCustom($type));
    }

    /**
     * @covers \JsonMapper\Helpers\ClassHelper
     */
    public function testNonExistingClassNameIsNotSeenAsCustomClass(): void
    {
        self::assertFalse(ClassHelper::isCustom('asdf'));
    }

    public function builtinClassDataProvider(): array
    {
        return [
            \DateTime::class . ' as class constant' => [\DateTime::class],
            \DateTime::class . ' as string' => ['\DateTime'],
            \DateTimeImmutable::class . ' as class constant' => [\DateTimeImmutable::class],
            \DateTimeImmutable::class . ' as string' => ['\DateTimeImmutable'],
        ];
    }

    public function customClassDataProvider(): array
    {
        return [
            ComplexObject::class . ' as class constant' => [ComplexObject::class],
            ComplexObject::class . ' as string' => ['\JsonMapper\Tests\Implementation\ComplexObject'],
        ];
    }
}
