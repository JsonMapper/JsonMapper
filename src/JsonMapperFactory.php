<?php

declare(strict_types=1);

namespace JsonMapper;

use JsonMapper\Handler\PropertyMapper;
use JsonMapper\Middleware\DocBlockAnnotations;
use JsonMapper\Middleware\FullQualifiedClassNameResolver;
use JsonMapper\Middleware\MiddlewareInterface;
use JsonMapper\Middleware\TypedProperties;

class JsonMapperFactory
{
    public function create(PropertyMapper $propertyMapper = null, MiddlewareInterface ...$handlers): JsonMapperInterface
    {
        $mapper = new JsonMapper($propertyMapper ?? new PropertyMapper());
        foreach ($handlers as $handler) {
            $mapper->push($handler);
        }

        return $mapper;
    }

    public function default(): JsonMapperInterface
    {
        return (new JsonMapper(new PropertyMapper()))
            ->push(new DocBlockAnnotations())
            ->push(new FullQualifiedClassNameResolver());
    }

    public function bestFit(): JsonMapperInterface
    {
        $mapper = new JsonMapper(new PropertyMapper());

        $mapper->push(new DocBlockAnnotations());

        if (PHP_VERSION_ID >= 70400) {
            $mapper->push(new TypedProperties());
        }

        $mapper->push(new FullQualifiedClassNameResolver());

        return $mapper;
    }
}
