<?php

declare(strict_types=1);

namespace JsonMapper;

use JsonMapper\Handler\PropertyMapper;
use JsonMapper\Middleware\MiddlewareInterface;

class JsonMapperFactory
{
    public function create(PropertyMapper $propertyMapper = null, MiddlewareInterface ...$handlers): JsonMapperInterface
    {
        $builder = JsonMapperBuilder::new()
            ->withPropertyMapper($propertyMapper ?? new PropertyMapper());
        foreach ($handlers as $handler) {
            $builder->withMiddleware($handler);
        }

        return $builder->build();
    }

    public function default(): JsonMapperInterface
    {
        return JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withNamespaceResolverMiddleware()
            ->build();
    }

    public function bestFit(): JsonMapperInterface
    {
        if (PHP_VERSION_ID <= 70400) {
            return $this->default();
        }

        return JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withTypedPropertiesMiddleware()
            ->withNamespaceResolverMiddleware()
            ->build();
    }
}
