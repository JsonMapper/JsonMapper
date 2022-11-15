<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Implementation\Php81;

use DateTime;

class WithConstructorReadOnlyDateTimePropertyPromotion
{
    public function __construct(
        public readonly DateTime $date
    ) {
    }
}