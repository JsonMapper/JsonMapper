---
permalink: /docs/guides/laravel-usage/
title: Laravel usage  
---

In order to use JsonMapper with your [Laravel](https://laravel.com){:target="_blank"} application you only need to 
add it to the service container which can be done using the [Service Providers](https://laravel.com/docs/7.x/providers){:target="_blank"} 

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JsonMapper\JsonMapper;
use JsonMapper\JsonMapperFactory;

class JsonMapperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(JsonMapper::class, function ($app) {
             return (new JsonMapperFactory())->default();
         });
    }
}
```

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