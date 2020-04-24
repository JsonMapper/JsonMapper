---
permalink: /  
title: Introduction  
---

JsonMapper is a PHP library that allows you to map a JSON response to your PHP objects that are either annotated using doc blocks or use typed properties.
```php
$mapper = (new \JsonMapper\JsonMapperFactory())->bestFit();
$object = new \Tests\JsonMapper\Implementation\SimpleObject();

$mapper->mapObject(json_decode('{ "name": "John Doe" }'), $object);

echo $object->getName(); // "John Doe"
```

# Why use JsonMapper
Continuously mapping your JSON responses to your own objects becomes tedious and is error prone. Not mentioning the
tests that needs to be written for said mapping.

JsonMapper has been build with the most common usages in mind. In order to allow for those edge cases which are not 
supported by default, it can easily be extended as its core has been designed using middleware.

JsonMapper supports the following features
 * **Case conversion**
 * **Debugging**
 * **DocBlock annotations**
 * **Final callback**
 * **Namespace resolving**
 * **PHP 7.4 Types properties**