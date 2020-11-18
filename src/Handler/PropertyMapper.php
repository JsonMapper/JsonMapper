<?php

declare(strict_types=1);

namespace JsonMapper\Handler;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;

class PropertyMapper
{
    /** @var ClassFactoryRegistry */
    private $classFactoryRegistry;

    public function __construct(ClassFactoryRegistry $classFactoryRegistry = null)
    {
        if ($classFactoryRegistry === null) {
            $classFactoryRegistry = new ClassFactoryRegistry();
            $classFactoryRegistry->loadNativePhpClassFactories();
        }

        $this->classFactoryRegistry = $classFactoryRegistry;
    }

    public function __invoke(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $propertyMap,
        JsonMapperInterface $mapper
    ): void {
        $values = (array) $json;
        foreach ($values as $key => $value) {
            if (! $propertyMap->hasProperty($key)) {
                continue;
            }

            $propertyInfo = $propertyMap->getProperty($key);
            $type = $propertyInfo->getPropertyType()->getType();

            if (! $propertyInfo->getPropertyType()->isNullable() && is_null($value)) {
                throw new \RuntimeException(
                    "Null provided in json where {$object->getName()}::{$key} doesn't allow null value"
                );
            }

            if ($propertyInfo->getPropertyType()->isNullable() && is_null($value)) {
                $this->setValue($object, $propertyInfo, null);
                continue;
            }

            if ($propertyInfo->getPropertyType()->isArray()) {
                $value = array_map(function ($value) use ($mapper, $type) {
                    return $this->mapPropertyValue($mapper, $type, $value);
                }, (array) $value);
            } else {
                $value = $this->mapPropertyValue($mapper, $type, $value);
            }

            $this->setValue($object, $propertyInfo, $value);
        }
    }

    /**
     * @param mixed $value
     */
    private function setValue(ObjectWrapper $object, Property $propertyInfo, $value): void
    {
        if ($propertyInfo->getVisibility()->equals(Visibility::PUBLIC())) {
            $object->getObject()->{$propertyInfo->getName()} = $value;
            return;
        }

        $setterMethod = 'set' . ucfirst($propertyInfo->getName());
        if (method_exists($object->getObject(), $setterMethod)) {
            $object->getObject()->$setterMethod($value);
            return;
        }

        throw new \RuntimeException(
            "{$object->getName()}::{$propertyInfo->getName()} is non-public and no setter method was found"
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function mapPropertyValue(JsonMapperInterface $mapper, string $type, $value)
    {
        if (ScalarType::isValid($type)) {
            return (new ScalarType($type))->cast($value);
        }

        if ($this->classFactoryRegistry->hasFactory($type)) {
            return $this->classFactoryRegistry->create($type, $value);
        }

        $instance = new $type();
        $mapper->mapObject($value, $instance);
        return $instance;
    }
}
