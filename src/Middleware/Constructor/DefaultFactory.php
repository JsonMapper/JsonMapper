<?php

declare(strict_types=1);

namespace JsonMapper\Middleware\Constructor;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\ValueFactory;
use JsonMapper\Helpers\DocBlockHelper;
use JsonMapper\Helpers\IScalarCaster;
use JsonMapper\Helpers\NamespaceHelper;
use JsonMapper\Helpers\UseStatementHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Parser\Import;
use JsonMapper\ValueObjects\AnnotationMap;
use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\ValueObjects\PropertyType;
use ReflectionMethod;

class DefaultFactory
{
    /** @var string */
    private $objectName;
    /** @var JsonMapperInterface */
    private $mapper;
    /** @var PropertyMap */
    private $propertyMap;
    /** @var ValueFactory */
    private $valueFactory;
    /** @var array<int, string> */
    private $parameterMap = [];
    /** @var array<string, mixed> */
    private $parameterDefaults = [];

    public function __construct(
        string $objectName,
        ReflectionMethod $reflectedConstructor,
        JsonMapperInterface $mapper,
        IScalarCaster $scalarCaster,
        FactoryRegistry $classFactoryRegistry,
        FactoryRegistry $nonInstantiableTypeResolver = null
    ) {
        $reflectedClass = $reflectedConstructor->getDeclaringClass();
        $this->objectName = $objectName;
        $this->mapper = $mapper;
        if ($nonInstantiableTypeResolver === null) {
            $nonInstantiableTypeResolver = new FactoryRegistry();
        }
        $this->propertyMap = new PropertyMap();
        $this->valueFactory = new ValueFactory($scalarCaster, $classFactoryRegistry, $nonInstantiableTypeResolver);

        $annotationMap = $this->getAnnotationMap($reflectedConstructor);
        $imports = UseStatementHelper::getImports($reflectedConstructor->getDeclaringClass());

        foreach ($reflectedConstructor->getParameters() as $param) {
            $builder = PropertyBuilder::new()
                ->setName($param->getName())
                ->setVisibility(Visibility::PUBLIC())
                ->setIsNullable($param->allowsNull());

            $type = 'mixed';
            $reflectedType = $param->getType();

            if (! \is_null($reflectedType)) {
                $type = $reflectedType->getName();
                if ($type === 'array') {
                    $builder->addType('mixed', ArrayInformation::singleDimension());
                } else {
                    $builder->addType($type, ArrayInformation::notAnArray());
                }
            }

            if ($annotationMap->hasParam($param->getName())) {
                $types = $this->deriveTypesFromDocBlockType($annotationMap->getParam($param->getName()), $reflectedClass, $imports);
                $builder->addTypes(...$types);
            }

            if ($reflectedClass->hasProperty($param->getName())) {
                $docComment = $reflectedClass->getProperty($param->getName())->getDocComment();
                if ($docComment !== false) {
                    $annotationMap = DocBlockHelper::parseDocBlockToAnnotationMap($docComment);
                    $types = $this->deriveTypesFromDocBlockType($annotationMap->getVar(), $reflectedClass, $imports);

                    $builder->addTypes(...$types);
                }
            }

            if (!$builder->hasAnyType()) {
                $builder->addType('mixed', ArrayInformation::notAnArray());
            }

            $this->propertyMap->addProperty($builder->build());
            $this->parameterMap[$param->getPosition()] = $param->getName();
            $this->parameterDefaults[$param->getName()] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
        }

        ksort($this->parameterMap);
    }

    public function __invoke(\stdClass $json)
    {
        $values = [];

        foreach ($this->parameterMap as $position => $name) {
            $values[$position] = $this->valueFactory->build(
                $this->mapper,
                $this->propertyMap->getProperty($name),
                $json->$name ?? $this->parameterDefaults[$name]
            );
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

    /**
     * @param Import[] $imports
     * @return PropertyType[]
     */
    private function deriveTypesFromDocBlockType(string $docBlockType, \ReflectionClass $class, array $imports): array
    {
        $types = [];

        if (strpos($docBlockType, '?') === 0) {
            $docBlockType = \substr($docBlockType, 1);
        }

        $docBlockTypes = \explode('|', $docBlockType);
        $docBlockTypes = \array_filter($docBlockTypes, static function (string $docBlockType) {
            return $docBlockType !== 'null';
        });

        foreach ($docBlockTypes as $dt) {
            $dt = \trim($dt);
            $isAnArrayType = \substr($dt, -2) === '[]';

            if (! $isAnArrayType) {
                $type = NamespaceHelper::resolveNamespace($dt, $class->getNamespaceName(), $imports);
                $types[] = new PropertyType($type, ArrayInformation::notAnArray());
                continue;
            }

            $initialBracketPosition = strpos($dt, '[');
            $dimensions = substr_count($dt, '[]');

            if ($initialBracketPosition !== false) {
                $type = substr($dt, 0, $initialBracketPosition);
            }

            $type = NamespaceHelper::resolveNamespace(
                $type,
                $class->getNamespaceName(),
                $imports
            );

            $types[] = new PropertyType($type, ArrayInformation::multiDimension($dimensions));
        }

        return $types;
    }
}
