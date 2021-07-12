<?php

declare(strict_types=1);

namespace JsonMapper\Wrapper;

class ObjectWrapper
{
    /** @var object */
    private $object;
    /** @var \ReflectionClass */
    private $reflectedObject;

    /** @param object $object */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /** @return object */
    public function getObject()
    {
        return $this->object;
    }

    public function getReflectedObject(): \ReflectionClass
    {
        if ($this->reflectedObject === null) {
            $this->reflectedObject = new \ReflectionClass($this->object);
        }

        return $this->reflectedObject;
    }

    public function getName(): string
    {
        return $this->getReflectedObject()->getName();
    }
}
