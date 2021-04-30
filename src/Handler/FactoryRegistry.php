<?php

declare(strict_types=1);

namespace JsonMapper\Handler;

use JsonMapper\Exception\ClassFactoryException;

class FactoryRegistry
{
    /** @var callable[] */
    private $factories = [];

    public function addFactory(string $className, callable $factory): self
    {
        if ($this->hasFactory($className)) {
            throw ClassFactoryException::forDuplicateClassname($className);
        }

        $this->factories[$this->sanitiseClassName($className)] = $factory;

        return $this;
    }

    public function hasFactory(string $className): bool
    {
        return array_key_exists($this->sanitiseClassName($className), $this->factories);
    }

    /**
     * @param mixed $params
     * @return mixed
     */
    public function create(string $className, $params)
    {
        if (!$this->hasFactory($className)) {
            throw ClassFactoryException::forMissingClassname($className);
        }

        $factory = $this->factories[$this->sanitiseClassName($className)];

        return $factory($params);
    }

    private function sanitiseClassName(string $className): string
    {
        /* Erase leading slash as ::class doesnt contain leading slash */
        if (strpos($className, '\\') === 0) {
            $className = substr($className, 1);
        }

        return $className;
    }
}
