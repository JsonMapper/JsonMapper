---
permalink: /docs/middleware/case-conversion/  
title: Case conversion  
---

The case conversion middleware can map from a specific text notation to another text notation.
 This way your code doesn't need to follow the same text notation as the JSON API exposes.
 
```php
# Converting studly caps to camel case
$mapper = (new \JsonMapper\JsonMapperFactory())->default();
$mapper->push(new \JsonMapper\Middleware\CaseConversion(
    \JsonMapper\Enums\TextNotation::STUDLY_CAPS(),
    \JsonMapper\Enums\TextNotation::CAMEL_CASE()
));
$object = new \Tests\JsonMapper\Implementation\SimpleObject();

$mapper->mapObject(json_decode('{ "Name": "John Doe" }'), $object);

echo $object->getName();
```  

The middleware currently supports the following text notations in the case conversion:
* **Studly caps**
* **Camel case**
* **Underscore**
* **Kebab case**