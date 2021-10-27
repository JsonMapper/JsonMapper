<?php

namespace JsonMapper\Tests\Implementation\Models;

use JsonMapper\Tests\Implementation\Models\Sub\AnotherValueHolder as Blub;

class NamespaceAliasObject
{
    /** @var ValueHolder */
    public $aVal;
    /** @var Blub */
    public $bVal;
}
