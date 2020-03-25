[![Build Status](https://api.travis-ci.com/DannyvdSluijs/JsonMapper.svg?branch=master)](https://travis-ci.com/DannyvdSluijs/JsonMapper) 
[![Coverage Status](https://coveralls.io/repos/github/DannyvdSluijs/JsonMapper/badge.svg)](https://coveralls.io/github/DannyvdSluijs/JsonMapper) 
[![Mergify Status](https://img.shields.io/endpoint.svg?url=https://dashboard.mergify.io/badges/DannyvdSluijs/JsonMapper&style=flat)](https://mergify.io)

# What is JsonMapper
JsonMapper allows you to easily map a JSON response to your own objects. Out of the box it can map to plain old PHP 
objects that are either annotated using doc blocks or typed properties, complete with namespace resolution based in 
the imports defined at the top of your class. This is done without any additional code in your classes.

_Example #1 Simple mapping_
```php
$mapper = (new \DannyVanDerSluijs\JsonMapper\JsonMapperFactory())->bestFit();
$object = new \DannyVanDerSluijs\Tests\JsonMapper\Implementation\SimpleObject();

$mapper->mapObject(json_decode('{ "name": "John Doe" }'), $object);

var_dump($object);
```
The above example will output:
```text
class DannyVanDerSluijs\Tests\JsonMapper\Implementation\SimpleObject#1 (1) {
  private $name =>
  string(8) "John Doe"
}
```  

# Why user JsonMapper
Continuously mapping your JSON responses to your own objects becomes tedious and is error prone. Not mentioning the
tests that needs to be written for said mapping.

# Customizing JsonMapper
JsonMapper has been build with the most common usages in mind. In order to allow for those edge cases which are not 
supported by default, JsonMapper can easily be extended as its core was designed using middleware. Writing your own 
middleware has been made as easy as possible with an `AbstractMiddleware` that can be extended with the functionality 
you need for your project.

_Example #2 Custom middleware_
```php
use DannyVanDerSluijs\JsonMapper;

$mapper = (new JsonMapper\JsonMapperFactory())->bestFit();
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
