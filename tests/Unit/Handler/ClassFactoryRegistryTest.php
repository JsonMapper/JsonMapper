<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Handler;

use JsonMapper\Exception\ClassFactoryException;
use JsonMapper\Handler\ClassFactoryRegistry;
use PHPUnit\Framework\TestCase;

class ClassFactoryRegistryTest extends TestCase
{

    public function testAddFactoryThrowsExceptionWhenDuplicateClassNameIsAdded(): void
    {
        $classFactoryRegistry = new ClassFactoryRegistry();
        $classFactoryRegistry->addFactory(__CLASS__, static function () {});

        $this->expectExceptionObject(ClassFactoryException::forDuplicateClassname(__CLASS__));

        $classFactoryRegistry->addFactory(__CLASS__, static function () {});
    }
}
