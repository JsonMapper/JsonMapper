---
permalink: /docs/guides/symfony-usage/
title: Symfony usage  
---

In order to use JsonMapper with your [Symfony](https://symfony.com){:target="_blank"} application you only need to 
add it to the DI container which can be done using the [ContainerConfigurator](https://symfony.com/doc/current/service_container/factories.html){:target="_blank"} 

```php
# config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JsonMapper\JsonMapper;
use JsonMapper\JsonMapperFactory;

return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(JsonMapperFactory::class);
    $services->set(JsonMapper::class)
        ->factory([ref(JsonMapperFactory::class), 'default']);
};
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