<?php

declare(strict_types=1);

namespace JsonMapper\Handler;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
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
            $type = $propertyInfo->getType();

			if ($propertyInfo->isNullable() && is_null($value)) {
				continue;
			}

            if ($propertyInfo->isArray()) {
                $value = array_map(function ($value) use ($mapper, $type) {
                    return $this->mapPropertyValue($mapper, $type, $value);
                }, (array) $value);
            } else {
                $value = $this->mapPropertyValue($mapper, $type, $value);
            }

            if ($propertyInfo->getVisibility()->equals(Visibility::PUBLIC())) {
                $object->getObject()->$key = $value;
                continue;
            }

            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($object->getObject(), $setterMethod)) {
                $object->getObject()->$setterMethod($value);
            }
        }
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
