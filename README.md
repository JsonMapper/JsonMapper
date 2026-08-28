<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://jsonmapper.net/images/jsonmapper-light.png">
  <img alt="JsonMapper logo" src="https://jsonmapper.net/images/jsonmapper.png">
</picture>

---
JsonMapper is a PHP library that allows you to map a JSON response to your PHP objects that are either annotated using doc blocks or use typed properties.
For more information see the project website: https://jsonmapper.net/

[![GitHub](https://img.shields.io/github/license/JsonMapper/JsonMapper)](https://choosealicense.com/licenses/mit/)
[![Packagist Version](https://img.shields.io/packagist/v/json-mapper/json-mapper)](https://packagist.org/packages/json-mapper/json-mapper)
![Packagist Downloads](https://img.shields.io/packagist/dm/json-mapper/json-mapper)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/json-mapper/json-mapper)](#)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/JsonMapper/JsonMapper/build.yaml)
[![Coverage Status](https://coveralls.io/repos/github/JsonMapper/JsonMapper/badge.svg?branch=develop)](https://coveralls.io/github/JsonMapper/JsonMapper?branch=develop)

# Why use JsonMapper
Continuously mapping your JSON responses to your own objects becomes tedious and is error-prone. Not mentioning the
tests that need to be written for said mapping.

JsonMapper has been built with the most common usages in mind. To allow for those edge cases which are not 
supported by default, it can easily be extended as its core has been designed using middleware.

JsonMapper supports the following features
 * [DocBlock annotations](https://jsonmapper.net/docs/doc-block-annotations) and [typed properties](https://jsonmapper.net/docs/typed-properties)
 * [Namespace resolving](https://jsonmapper.net/docs/namespace-resolver), so your models can stay organised the way you want them
 * [Custom constructors](https://jsonmapper.net/docs/constructor), including readonly properties and classes on PHP 8.1+
 * Enums on PHP 8.1+
 * Mapping from names that do not match your model, through [PHP attributes](https://jsonmapper.net/docs/attributes), an explicit [rename](https://jsonmapper.net/docs/rename) mapping, or [case conversion](https://jsonmapper.net/docs/case-conversion)
 * [Value transformation](https://jsonmapper.net/docs/value-transformation) through a callback
 * [Interfaces](https://jsonmapper.net/docs/interfaces) and [abstract classes](https://jsonmapper.net/docs/abstracts), through factories you register
 * [Strict scalar casting](https://jsonmapper.net/docs/casting-values), which throws rather than coercing a mismatched type
 * [A final callback](https://jsonmapper.net/docs/final-callback) once an object is filled, and [debug logging](https://jsonmapper.net/docs/debugging) of the mapping as it happens
 * First-party [Laravel](https://jsonmapper.net/docs/laravel-usage) and [Symfony](https://jsonmapper.net/docs/symfony-usage) integrations, plus mapping straight onto [Eloquent models](https://jsonmapper.net/docs/laravel-eloquent)

# Installing JsonMapper
The installation of JsonMapper can easily be done with [Composer](https://getcomposer.org)
```bash
$ composer require json-mapper/json-mapper
```
The example shown above assumes that `composer` is on your `$PATH`.

# How to use JsonMapper
Given the following class definition
```php
class User
{
    /** @var string */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```
Combined with the following JsonMapper code as part of your application
```php
$mapper = (new \JsonMapper\JsonMapperFactory())->default();
$object = new User();

$mapper->mapObject(json_decode('{ "name": "John Doe" }'), $object);

echo $object->getName(); // "John Doe"
```
The property is private, so JsonMapper fills it through `setName()`. A non-public property with no
matching setter raises a `RuntimeException`, so your model keeps its encapsulation either way.

# Customizing JsonMapper
Writing your own middleware has been made as easy as possible with an `AbstractMiddleware` that can be extended with the functionality 
you need for your project.

```php
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

# Contributing
Please refer to [CONTRIBUTING.md](https://github.com/JsonMapper/JsonMapper/blob/main/CONTRIBUTING.md) for information on how to contribute to JsonMapper.

## List of Contributors
Thanks to everyone who has contributed to JsonMapper! You can find a detailed list of people that contributed to JsonMapper on [GitHub](https://github.com/JsonMapper/JsonMapper/graphs/contributors).

## Sponsoring
[![JetBrains logo.](https://resources.jetbrains.com/storage/products/company/brand/logos/jetbrains.svg)](https://jb.gg/OpenSource)

This project is sponsored by JetBrains providing a license for PhpStorm to continue building on JsonMapper.     

# License
The MIT License (MIT). Please see [License File](https://github.com/JsonMapper/JsonMapper/blob/main/LICENSE) for more information.
