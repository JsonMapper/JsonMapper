<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Handler;

use JsonMapper\Handler\DefaultClassFactoryRegistry;
use PHPUnit\Framework\TestCase;

class DefaultClassFactoryRegistryTest extends TestCase
{
    /**
     * @covers \JsonMapper\Handler\DefaultClassFactoryRegistry
     */
    public function testDefaultClassFactoryRegistryAddsFactoriesForNativeClasses(): void
    {
        $classFactoryRegistry = new DefaultClassFactoryRegistry();

        self::assertTrue($classFactoryRegistry->hasFactory(\DateTime::class));
        self::assertTrue($classFactoryRegistry->hasFactory(\DateTimeImmutable::class));
        self::assertTrue($classFactoryRegistry->hasFactory(\stdClass::class));
        self::assertEquals(new \DateTime('today'), $classFactoryRegistry->create(\DateTime::class, 'today'));
        self::assertEquals(
            new \DateTimeImmutable('today'),
            $classFactoryRegistry->create(\DateTimeImmutable::class, 'today')
        );
        self::assertEquals(
            (object) ['one' => 1, 'two' => 2],
            $classFactoryRegistry->create(\stdClass::class, ['one' => 1, 'two' => 2])
        );
    }
}
