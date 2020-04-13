---
permalink: /docs/architecture
title: Architecture
---

The core of JsonMapper is build using the chain of responsibility pattern allowing multiple
middleware being added to the mapper. This pattern allows for easy customisation for each
individual project.
This also allows for custom middleware to meet edge cases not offered in the middleware that is par of JsonMapper.

```php
$mapper = new \JsonMapper\JsonMapper(new \JsonMapper\Handler\PropertyMapper());

/* Push included middleware onto the mapper */
$mapper->push(new \JsonMapper\Middleware\DocBlockAnnotations());
$mapper->push(new \JsonMapper\Middleware\NamespaceResolver());

/* Add custom middleware */
$mapper->push(new class extends JsonMapper\Middleware\AbstractMiddleware {
    public function handle(
        \stdClass $json,
        JsonMapper\Wrapper\ObjectWrapper $object,
        JsonMapper\ValueObjects\PropertyMap $map,
        JsonMapper\JsonMapperInterface $mapper
    ): void {
        /* Custom logic here */
    }
});
```

## Supported PHP versions
JsonMapper follows the supported versions of [PHP.net](https://www.php.net/supported-versions.php){:target="_blank"}
and currently supports PHP versions 7.2 and higher. 