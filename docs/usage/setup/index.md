---
permalink: /docs/usage/setup
title: Setup
---

Setting up JsonMapper for your project is simple. JsonMapper comes with a factory that
offers three methods to create a JsonMapper instance.

```php
<?php

// Simply use `default` which offers the most light weigth JsonMapper
$default = (new \JsonMapper\JsonMapperFactory())->default();

// Use the `bestFit` to get the JsonMapper that fits best 
// to your PHP runtime version.  
$bestfit = (new \JsonMapper\JsonMapperFactory())->bestFit();

// Use `create` to build a new instance with a custom 
// property mapper and series of middleware
$custom = (new \JsonMapper\JsonMapperFactory())->create(
  new PropertyMapper, 
  new \JsonMapper\Middleware\DocBlockAnnotations(),   
  ...
);
```  