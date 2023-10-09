<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Helpers;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Helpers\ClassHelper;
use JsonMapper\Helpers\DocBlockHelper;
use JsonMapper\Tests\Implementation\ComplexObject;
use PHPUnit\Framework\TestCase;

class DocBlockHelperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Helpers\DocBlockHelper
     */
    public function testCanParseVarAnnotationBlock(): void
    {
        $docBlock = <<<EOD
/**
 * @var bool \$isEnabled 
 */
EOD;
        $result = DocBlockHelper::parseDocBlockToAnnotationMap($docBlock);

        self::assertTrue($result->hasVar());
        self::assertEquals('bool', $result->getVar());
        self::assertFalse($result->hasReturn());
        self::assertEmpty($result->getParams());
    }

    /**
     * @covers \JsonMapper\Helpers\DocBlockHelper
     */
    public function testCanParseVarAnnotationBlockWithNullableType(): void
    {
        $docBlock = <<<EOD
/**
 * @var ?bool \$isEnabled 
 */
EOD;
        $result = DocBlockHelper::parseDocBlockToAnnotationMap($docBlock);

        self::assertTrue($result->hasVar());
        self::assertEquals('?bool', $result->getVar());
        self::assertFalse($result->hasReturn());
        self::assertEmpty($result->getParams());
    }

    /**
     * @covers \JsonMapper\Helpers\DocBlockHelper
     */
    public function testCanParseParamsAnnotationBlock(): void
    {
        $docBlock = <<<EOD
/**
 * @param bool \$isEnabled
 * @param string \$name
 */
EOD;
        $result = DocBlockHelper::parseDocBlockToAnnotationMap($docBlock);

        self::assertTrue($result->hasParam('isEnabled'));
        self::assertEquals('bool', $result->getParam('isEnabled'));
        self::assertTrue($result->hasParam('name'));
        self::assertEquals('string', $result->getParam('name'));
        self::assertFalse($result->hasVar());
        self::assertFalse($result->hasReturn());
    }

    /**
     * @covers \JsonMapper\Helpers\DocBlockHelper
     */
    public function testCanParseEmptyString(): void
    {
        $docBlock = '';
        $result = DocBlockHelper::parseDocBlockToAnnotationMap($docBlock);

        self::assertFalse($result->hasVar());
        self::assertEmpty($result->getParams());
        self::assertFalse($result->hasReturn());
    }
}
