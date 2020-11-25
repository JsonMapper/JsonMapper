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

            $property = $propertyMap->getProperty($key);

            if (! $property->isNullable() && is_null($value)) {
                throw new \RuntimeException(
                    "Null provided in json where {$object->getName()}::{$key} doesn't allow null value"
                );
            }

            if ($property->isNullable() && is_null($value)) {
                $this->setValue($object, $property, null);
                continue;
            }

            $value = $this->mapPropertyValue($mapper, $property, $value);
            $this->setValue($object, $property, $value);
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
    private function mapPropertyValue(JsonMapperInterface $mapper, Property $property, $value)
    {
        /* @todo implement the union mapping */
        // For union types, loop through and see if value is a match with the type
//        if (count($property->getPropertyTypes()) > 1) {
//            foreach ($property->getPropertyTypes() as $type) {
//                /* If one side is an array and the other isn't continue to next type */
//                if (is_array($value) !== $type->isArray() && is_a($value, \stdClass::class) !== $type->isArray()) {
//                    continue;
//                }
//
//                /* Array of scalar values */
//                $copy = (array) $value;
//                $first = array_shift($copy);
//                if ($type->isArray() && $this->propertyTypeAndValueTypeAreScalarAndSameType($type, $first)) {
//                    $scalarType = new ScalarType($type->getType());
//                    return array_map(static function($v) use ($scalarType) { return $scalarType->cast($v); }, (array) $value);
//                }
//
//                if (! $type->isArray() && $this->propertyTypeAndValueTypeAreScalarAndSameType($type, $value)) {
//                    return (new ScalarType($type->getType()))->cast($value);
//                }
//
//                if (! $type->isArray() && $this->classFactoryRegistry->hasFactory($type->getType())) {
//                    return $this->classFactoryRegistry->create($type->getType(), $value);
//                }
//
//                if (class_exists($type->getType())) {
//                    $className = $type->getType();
//                    $instance = new $className();
//                    $mapper->mapObject($value, $instance);
//                    return $instance;
//                }
//            }
//        }

        // No match was found lets assume the first is the right one.
        $types = $property->getPropertyTypes();
        $type = array_shift($types);

        if (ScalarType::isValid($type->getType())) {
            return $this->mapToScalarValue($type->getType(), $value, $type->isArray());
        }

        if ($this->classFactoryRegistry->hasFactory($type->getType())) {
            if ($type->isArray()) {
                return array_map(function($v) use ($type) {  return $this->classFactoryRegistry->create($type->getType(), $v); }, $value);
            }
            return $this->classFactoryRegistry->create($type->getType(), $value);
        }

        return $this->mapToObject($type->getType(), $value, $type->isArray(), $mapper);
    }

    /**
     * @param mixed $value
     */
//    private function propertyTypeAndValueTypeAreScalarAndSameType(PropertyType $type, $value): bool
//    {
//        if (! is_scalar($value) || ! ScalarType::isValid($type->getType())) {
//            return false;
//        }
//
//        $valueType = gettype($value);
//        if ($valueType === 'double') {
//            $valueType = 'float';
//        }
//
//        return $type->getType() === $valueType;
//    }

    private function mapToScalarValue(string $type, $value, bool $asArray)
    {
        $scalar = new ScalarType($type);

        if ($asArray) {
            return array_map(function($v) use ($scalar) { return $scalar->cast($v); }, (array) $value);
        }

        return $scalar->cast($value);
    }

    private function mapToObject(string $type, $value, bool $asArray, JsonMapperInterface $mapper)
    {
        if ($asArray) {
            return array_map(
                static function($v) use ($type, $mapper) {
                    $instance = new $type();
                    $mapper->mapObject($v, $instance);
                    return $instance;
                },
                (array) $value
            );
        }

        $instance = new $type();
        $mapper->mapObject($value, $instance);
        return $instance;
    }
}
