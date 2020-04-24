---
permalink: /docs/middleware/debugging/  
title: Debugging  
---

The debugging middleware allows you to log the current state of the ongoing map method. 
The state of the json and object inputs as well as the property map will be logged to an [PSR-3 compliant](https://www.php-fig.org/psr/psr-3/){:target="_blank"} logger
 
```php
$mapper = (new \JsonMapper\JsonMapperFactory())->default();

# Add the debug middleware with an PSR compliant logger
$logger = new \Psr\Log\Test\TestLogger();
$mapper->push(new \JsonMapper\Middleware\Debugger($logger));

$object = new \Tests\JsonMapper\Implementation\SimpleObject();
$mapper->mapObject(json_decode('{ "Name": "John Doe" }'), $object);

var_dump($logger->records);
```