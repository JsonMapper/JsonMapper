<?php

declare(strict_types=1);

namespace JsonMapper\Middleware\Constructor;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Helpers\DocBlockHelper;
use JsonMapper\Helpers\IScalarCaster;
use JsonMapper\Helpers\NamespaceHelper;
use JsonMapper\Helpers\UseStatementHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\AnnotationMap;
use ReflectionMethod;

class DefaultFactory
{
    /** @var string */
    private $objectName;
    /** @var JsonMapperInterface */
    private $mapper;
    /** @var IScalarCaster */
    private $scalarCaster;
    /** @var Parameter[] */
    private $parameters = [];

    public function __construct(
        string $objectName,
        ReflectionMethod $reflectedConstructor,
        JsonMapperInterface $mapper,
        IScalarCaster $scalarCaster,
        FactoryRegistry $classFactoryRegistry
    ) {
        $this->objectName = $objectName;
        $this->mapper = $mapper;
        $this->scalarCaster = $scalarCaster;
        $this->classFactoryRegistry = $classFactoryRegistry;

        $annotationMap = $this->getAnnotationMap($reflectedConstructor);

        foreach ($reflectedConstructor->getParameters() as $param) {
            $type = 'mixed';
            $reflectedType = $param->getType();
            if (! \is_null($reflectedType)) {
                $type = $reflectedType->getName();
            }
            if ($annotationMap->hasParam($param->getName())) {
                $type = $annotationMap->getParam($param->getName());
                if (substr($type, -2) === '[]') {
                    $type = substr($type, 0, -2);
                }
                $imports = UseStatementHelper::getImports($reflectedConstructor->getDeclaringClass());
                $type = NamespaceHelper::resolveNamespace(
                    $type,
                    $reflectedConstructor->getDeclaringClass()->getNamespaceName(),
                    $imports
                );
            }
            $this->parameters[] = new Parameter(
                $param->getName(),
                $type,
                $param->getPosition(),
                $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
            );
        }

        usort(
            $this->parameters,
            static function (Parameter $left, Parameter $right) {
                return $left->getPosition() - $right->getPosition();
            }
        );
    }

    public function __invoke(\stdClass $json)
    {
        // @todo Basically we are reproducing the logic which is inside the PropertyMapper::mapPropertyValue function
        $values = [];

        foreach ($this->parameters as $parameter) {
            $type = $parameter->getType();
            $name = $parameter->getName();
            $value = $parameter->getDefaultValue();

            if (isset($json->$name)) {
                $value = $json->$name;
            }

            if ($this->classFactoryRegistry->hasFactory($type)) {
                $value =  $this->classFactoryRegistry->create($type, $value);
            }

            if ($value instanceof \stdClass && class_exists($type)) {
                $value =  $this->mapper->mapToClass($value, $type);
            }

            if (is_array($value) && $value[0] instanceof \stdClass && class_exists($type)) {
                $value = $this->mapper->mapToClassArray($value, $type);
            }


            if (PHP_VERSION_ID >= 80100 && (is_string($value) || is_int($value)) && enum_exists($type)) {
                $value = call_user_func("{$type}::from", $value);
            }

            if (is_scalar($value) && gettype($value) !== $parameter->getType() && ScalarType::isValid($parameter->getType())) {
                $value = $this->scalarCaster->cast(new ScalarType($parameter->getType()), $value);
            }

            $values[$parameter->getPosition()] = $value;
        }

        return new $this->objectName(...$values);
    }

    private function getAnnotationMap(ReflectionMethod $reflectedConstructor): AnnotationMap
    {
        $docBlock = $reflectedConstructor->getDocComment();
        $annotationMap = new AnnotationMap();
        if ($docBlock) {
            $annotationMap = DocBlockHelper::parseDocBlockToAnnotationMap($docBlock);
        }
        return $annotationMap;
    }
}
