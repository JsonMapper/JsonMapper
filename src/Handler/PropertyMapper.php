<?php

declare(strict_types=1);

namespace JsonMapper\Handler;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Enums\Visibility;
use JsonMapper\Exception\ClassFactoryException;
use JsonMapper\Exception\TypeError;
use JsonMapper\Helpers\IScalarCaster;
use JsonMapper\Helpers\ScalarCaster;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\ValueObjects\PropertyType;
use JsonMapper\Wrapper\ObjectWrapper;

class PropertyMapper
{
    /** @var FactoryRegistry */
    private $classFactoryRegistry;
    /** @var FactoryRegistry */
    private $nonInstantiableTypeResolver;
    /** @var IScalarCaster */
    private $scalarCaster;

    public function __construct(
        FactoryRegistry $classFactoryRegistry = null,
        FactoryRegistry $nonInstantiableTypeResolver = null,
        IScalarCaster $casterHelper = null
    ) {
        if ($classFactoryRegistry === null) {
            $classFactoryRegistry = FactoryRegistry::withNativePhpClassesAdded();
        }

        if ($nonInstantiableTypeResolver === null) {
            $nonInstantiableTypeResolver = new FactoryRegistry();
        }
        if ($casterHelper === null) {
            $casterHelper = new ScalarCaster();
        }

        $this->classFactoryRegistry = $classFactoryRegistry;
        $this->nonInstantiableTypeResolver = $nonInstantiableTypeResolver;
        $this->scalarCaster = $casterHelper;
    }

    public function __invoke(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $propertyMap,
        JsonMapperInterface $mapper
    ): void {
        // If the type we are mapping has a last minute factory use it.
        if ($this->classFactoryRegistry->hasFactory($object->getName())) {
            $result = $this->classFactoryRegistry->create($object->getName(), $json);

            $object->setObject($result);
            return;
        }

        $values = (array) $json;
        foreach ($values as $key => $value) {
            if (! $propertyMap->hasProperty($key)) {
                continue;
            }

            $property = $propertyMap->getProperty($key);

            if (! $property->isNullable() && \is_null($value)) {
                throw new \RuntimeException(
                    "Null provided in json where {$object->getName()}::{$key} doesn't allow null value"
                );
            }

            if ($property->isNullable() && \is_null($value)) {
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

        $methodName = 'set' . \ucfirst($propertyInfo->getName());
        if (\method_exists($object->getObject(), $methodName)) {
            $method = new \ReflectionMethod($object->getObject(), $methodName);
            $parameters = $method->getParameters();

            if (\is_array($value) && \count($parameters) === 1 && $parameters[0]->isVariadic()) {
                $object->getObject()->$methodName(...$value);
                return;
            }

            $object->getObject()->$methodName($value);
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
        // For union types, loop through and see if value is a match with the type
        if (\count($property->getPropertyTypes()) > 1) {
            foreach ($property->getPropertyTypes() as $type) {
                if (\is_array($value) && $type->isArray() && count($value) === 0) {
                    return [];
                }

                if (\is_array($value) && $type->isArray()) {
                    $copy = $value;
                    $firstValue = \array_shift($copy);

                    /* Array of scalar values */
                    if ($this->propertyTypeAndValueTypeAreScalarAndSameType($type, $firstValue)) {
                        $scalarType = new ScalarType($type->getType());
                        return \array_map(function ($v) use ($scalarType) {
                            return $this->scalarCaster->cast($scalarType, $v);
                        }, $value);
                    }

                    // Array of registered class @todo how do you know it was the correct type?
                    if ($this->classFactoryRegistry->hasFactory($type->getType())) {
                        return \array_map(function ($v) use ($type) {
                            return $this->classFactoryRegistry->create($type->getType(), $v);
                        }, $value);
                    }

                    // Array of existing class @todo how do you know it was the correct type?
                    if (\class_exists($type->getType())) {
                        return \array_map(
                            static function ($v) use ($type, $mapper) {
                                $className = $type->getType();
                                $instance = new $className();
                                $mapper->mapObject($v, $instance);
                                return $instance;
                            },
                            $value
                        );
                    }

                    continue;
                }

                // Single scalar value
                if ($this->propertyTypeAndValueTypeAreScalarAndSameType($type, $value)) {
                    return $this->scalarCaster->cast(new ScalarType($type->getType()), $value);
                }

                // Single registered class @todo how do you know it was the correct type?
                if ($this->classFactoryRegistry->hasFactory($type->getType())) {
                    return $this->classFactoryRegistry->create($type->getType(), $value);
                }

                // Single existing class @todo how do you know it was the correct type?
                if (\class_exists($type->getType())) {
                    return $this->mapToSingleObject($type->getType(), $value, $mapper);
                }
            }
        }

        // No match was found (or there was only one option) lets assume the first is the right one.
        $types = $property->getPropertyTypes();
        $type = \array_shift($types);

        if ($type === null) {
            // Return the value as is as there is no type info.
            return $value;
        }

        if (ScalarType::isValid($type->getType())) {
            return $this->mapToScalarValues($type, $value);
        }

        if (PHP_VERSION_ID >= 80100 && enum_exists($type->getType())) {
            return $this->mapToEnum($type, $value);
        }

        if ($this->classFactoryRegistry->hasFactory($type->getType())) {
            return $this->mapToObjectsUsingFactory($type, $value);
        }

        if ((class_exists($type->getType()) || interface_exists($type->getType()))) {
            return $this->mapToObjects($type, $value, $mapper);
        }

        throw new \Exception("Unable to map to {$type->getType()}");
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true scalar $value
     */
    private function propertyTypeAndValueTypeAreScalarAndSameType(PropertyType $type, $value): bool
    {
        if (! \is_scalar($value) || ! ScalarType::isValid($type->getType())) {
            return false;
        }

        $valueType = \gettype($value);
        if ($valueType === 'double') {
            $valueType = 'float';
        }

        return $type->getType() === $valueType;
    }

    /**
     * @param mixed $value
     * @return string|bool|int|float
     */
    private function mapToSingleScalarValue(string $type, $value)
    {
        $scalar = new ScalarType($type);

        return $this->scalarCaster->cast($scalar, $value);
    }

    /**
     * @param mixed $value
     * @return array<int, string|bool|int|float|array>
     */
    private function mapToArrayOfScalarValue(string $type, $value): array
    {
        $scalar = new ScalarType($type);
        return \array_map(function ($v) use ($type, $scalar) {
            return $this->scalarCaster->cast($scalar, $v);
        }, (array) $value);
    }

    /**
     * @param mixed $value
     * @return array<int, string|bool|int|float>
     */
    private function recursiveMapToArrayOfScalarValue(string $type, $value): array
    {
        $scalar = new ScalarType($type);
        return \array_map(function ($v) use ($type, $scalar) {
            if (is_array($v)) {
                return $this->recursiveMapToArrayOfScalarValue($type, $v);
            }

            return $this->scalarCaster->cast($scalar, $v);
        }, (array) $value);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return T
     */
    private function mapToSingleEnum(string $type, $value)
    {
        return call_user_func("{$type}::from", $value);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed
     * @return array<int, T>
     */
    private function mapToArrayOfEnum(string $type, $value): array
    {
        return \array_map(function ($v) use ($type) {
            return $this->mapToSingleEnum($type, $v);
        }, (array) $value);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed $value
     */
    private function recursiveMapToArrayOfEnum(string $type, $value): array
    {
        return \array_map(function ($v) use ($type) {
            if (is_array($v)) {
                return $this->recursiveMapToArrayOfEnum($type, $v);
            }

            return $this->mapToSingleEnum($type, $v);
        }, (array) $value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function mapToSingleObjectUsingFactory(string $type, $value)
    {
        return $this->classFactoryRegistry->create($type, $value);
    }

    private function mapToArrayOfObjectsUsingFactory(string $type, $value): array
    {
        return \array_map(function ($v) use ($type) {
            return $this->mapToSingleObjectUsingFactory($type, $v);
        }, (array) $value);
    }

    private function recursiveMapToArrayOfObjectsUsingFactory(string $type, $value): array
    {
        return \array_map(function ($v) use ($type) {
            if (is_array($v)) {
                return $this->recursiveMapToArrayOfObjectsUsingFactory($type, $v);
            }

            return $this->mapToSingleObjectUsingFactory($type, $v);
        }, (array) $value);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return array<int, T|array>
     */
    private function recursiveMapToArrayOfObjects(string $type, $value, JsonMapperInterface $mapper): array
    {
        return \array_map(function ($v) use ($type, $mapper) {
            if (is_array($v)) {
                return $this->recursiveMapToArrayOfObjects($type, $v, $mapper);
            }

            return $this->mapToSingleObject($type, $v, $mapper);
        }, (array) $value);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return array<int, T>
     */
    private function mapToArrayOfObjects(string $type, $value, JsonMapperInterface $mapper): array
    {
        return \array_map(function ($v) use ($type, $mapper) {
            return $this->mapToSingleObject($type, $v, $mapper);
        }, (array) $value);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return T
     */
    private function mapToSingleObject(string $type, $value, JsonMapperInterface $mapper)
    {
        if (! class_exists($type) && ! interface_exists($type)) {
            throw TypeError::forArgument(__METHOD__, 'class-string', $type, 1, '$type');
        }

        $reflectionType = new \ReflectionClass($type);
        if (!$reflectionType->isInstantiable()) {
            return $this->resolveUnInstantiableType($type, $value, $mapper);
        }

        $instance = new $type();
        $mapper->mapObject($value, $instance);
        return $instance;
    }

    /**
     * @template T
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return T
     */
    private function resolveUnInstantiableType(string $type, $value, JsonMapperInterface $mapper)
    {
        try {
            $instance = $this->nonInstantiableTypeResolver->create($type, $value);
            $mapper->mapObject($value, $instance);
            return $instance;
        } catch (ClassFactoryException $e) {
            throw new \RuntimeException(
                "Unable to resolve un-instantiable {$type} as no factory was registered",
                0,
                $e
            );
        }
    }

    private function mapToObjects(PropertyType $type, $value, JsonMapperInterface $mapper)
    {
        if ($type->isMultiDimensionalArray()) {
            return $this->recursiveMapToArrayOfObjects($type->getType(), $value, $mapper);
        }
        if ($type->isArray()) {
            return $this->mapToArrayOfObjects($type->getType(), $value, $mapper);
        }
        return $this->mapToSingleObject($type->getType(), $value, $mapper);
    }

    private function mapToObjectsUsingFactory(PropertyType $type, $value)
    {
        if ($type->isMultiDimensionalArray()) {
            return $this->recursiveMapToArrayOfObjectsUsingFactory($type->getType(), $value);
        }
        if ($type->isArray()) {
            return $this->mapToArrayOfObjectsUsingFactory($type->getType(), $value);
        }
        return $this->mapToSingleObjectUsingFactory($type->getType(), $value);
    }

    private function mapToEnum(PropertyType $type, $value)
    {
        if ($type->isMultiDimensionalArray()) {
            return $this->recursiveMapToArrayOfEnum($type->getType(), $value);
        }
        if ($type->isArray()) {
            return $this->mapToArrayOfEnum($type->getType(), $value);
        }
        return $this->mapToSingleEnum($type->getType(), $value);
    }

    private function mapToScalarValues(PropertyType $type, $value)
    {
        if ($type->isMultiDimensionalArray()) {
            return $this->recursiveMapToArrayOfScalarValue($type->getType(), $value);
        }
        if ($type->isArray()) {
            return $this->mapToArrayOfScalarValue($type->getType(), $value);
        }
        return $this->mapToSingleScalarValue($type->getType(), $value);
    }
}
