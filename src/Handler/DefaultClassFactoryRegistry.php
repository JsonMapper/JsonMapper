<?php

declare(strict_types=1);

namespace JsonMapper\Handler;

class DefaultClassFactoryRegistry extends FactoryRegistry
{
    public function __construct()
    {
        $this->addFactory(\DateTime::class, static function (string $value) {
            return new \DateTime($value);
        });
        $this->addFactory(\DateTimeImmutable::class, static function (string $value) {
            return new \DateTimeImmutable($value);
        });
        $this->addFactory(\stdClass::class, static function ($value) {
            return (object) $value;
        });
    }


}
