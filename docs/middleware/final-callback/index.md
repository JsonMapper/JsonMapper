---
permalink: /docs/middleware/final-callback/  
title: Final callback  
---

Using the final callback middleware it is possible to invoke a callback because you might need to initialise soe method on you model or perhaps want to put it into cache.

```php
$logger = new \Psr\Log\Test\TestLogger();
$mapper = (new \JsonMapper\JsonMapperFactory())->default();

# Add the callback middleware
$mapper->push(new \JsonMapper\Middleware\FinalCallback(function(
    \stdClass $json,
    \JsonMapper\Wrapper\ObjectWrapper $object,
    \JsonMapper\ValuerObject\PropertyMap $map,
    \JsonMapper\JsonMapperInterface $mapper
) {
    // Call a method on the object
    $object->getObject()->done();
    // Or persist it in the cache
    Cache::put('key', $object->getObject(), $seconds);
}));

$object = new \Tests\JsonMapper\Implementation\SimpleObject();
$mapper->mapObject(json_decode('{ "Name": "John Doe" }'), $object);
``` 