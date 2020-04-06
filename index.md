---
permalink: /  
title: Introduction  
---

JsonMapper allows you to easily map a JSON response to your PHP objects that are either annotated using doc blocks 
or contain typed properties, powered with namespace resolution based in the imports defined at the top of your class. 

```php
$mapper = (new \JsonMapper\JsonMapperFactory())->bestFit();
$object = new \Tests\JsonMapper\Implementation\SimpleObject();

$mapper->mapObject(json_decode('{ "name": "John Doe" }'), $object);

var_dump($object);

//class JsonMapper\Tests\Implementation\SimpleObject#1 (1) {
//  private $name =>
//  string(8) "John Doe"
//}
```
