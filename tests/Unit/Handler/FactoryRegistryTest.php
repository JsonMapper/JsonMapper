<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Handler;

use JsonMapper\Exception\ClassFactoryException;
use JsonMapper\Handler\FactoryRegistry;
use PHPUnit\Framework\TestCase;

class FactoryRegistryTest extends TestCase
{
    /**
     * @covers \JsonMapper\Handler\FactoryRegistry
     */
    public function testAddFactoryAddsFactory(): void
    {
        $classFactoryRegistry = new FactoryRegistry();
        $classFactoryRegistry->addFactory(__CLASS__, static function () {
        });

        self::assertTrue($classFactoryRegistry->hasFactory(__CLASS__));
    }

    /**
     * @covers \JsonMapper\Handler\FactoryRegistry
     */
    public function testHasFactoryReturnsFalseWhenNoFactoryRegistered(): void
    {
        $classFactoryRegistry = new FactoryRegistry();

        self::assertFalse($classFactoryRegistry->hasFactory(__CLASS__));
    }

    /**
     * @covers \JsonMapper\Handler\FactoryRegistry
     */
    public function testAddFactoryThrowsExceptionWhenDuplicateClassNameIsAdded(): void
    {
        $classFactoryRegistry = new FactoryRegistry();
        $classFactoryRegistry->addFactory(__CLASS__, static function () {
        });

        $this->expectExceptionObject(ClassFactoryException::forDuplicateClassname(__CLASS__));

        $classFactoryRegistry->addFactory(__CLASS__, static function () {
        });
    }

    /**
     * @covers \JsonMapper\Handler\FactoryRegistry
     */
    public function testCreateReturnsValueFromCallable(): void
    {
        $classFactoryRegistry = new FactoryRegistry();
        $object = new \stdClass();
        $classFactoryRegistry->addFactory(__CLASS__, static function () use ($object) {
            return $object;
        });

        self::assertSame($object, $classFactoryRegistry->create(__CLASS__, new \stdClass()));
    }

    /**
     * @covers \JsonMapper\Handler\FactoryRegistry
     */
    public function testCreateCanHandleLeadingSlash(): void
    {
        $classFactoryRegistry = new FactoryRegistry();
        $object = new \stdClass();
        $classFactoryRegistry->addFactory(\DateTimeImmutable::class, static function () use ($object) {
            return $object;
        });

        self::assertSame($object, $classFactoryRegistry->create('\DateTimeImmutable', new \stdClass()));
    }

    /**
     * @covers \JsonMapper\Handler\FactoryRegistry
     */
    public function testCreateThrowsExceptionForMissingFactory(): void
    {
        $classFactoryRegistry = new FactoryRegistry();

        $this->expectExceptionObject(ClassFactoryException::forMissingClassname(__CLASS__));

        $classFactoryRegistry->create(__CLASS__, new \stdClass());
    }
}
