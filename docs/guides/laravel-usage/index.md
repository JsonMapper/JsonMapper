---
permalink: /docs/guides/laravel-usage/
title: Laravel usage  
---

In order to use JsonMapper with your [Laravel](https://laravel.com){:target="_blank"} application you only need 
JsonMapper's [LaravelPackage](https://github.com/JsonMapper/LaravelPackage){:target="_blank"}. 

The installation of JsonMapper Laravel package can easily be done with [Composer](https://getcomposer.org){:target="_blank"}
```bash
$ composer require json-mapper/laravel-package
```
The example shown above assumes that `composer` is on your `$PATH`.

Now JsonMapper will be automatically injected if it is provided as one of the constructor arguments.

```php
<?php

namespace App\Service;

use JsonMapper\JsonMapper;

class ApiClient
{
    /** @var JsonMapper */
    private $mapper;
    
    public function __construct(JsonMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
``` 