<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

/**
 * @psalm-immutable
 */
class AnnotationMap
{
    /** @var string|null */
    private $var;
    /** @var string[] */
    private $params = [];
    /** @var string|null */
    private $return;

    public function __construct(?string $var = null, array $params = [], ?string $return = null)
    {
        $this->var = $var;
        $this->params = $params;
        $this->return = $return;
    }

    public function hasVar(): bool
    {
        return ! \is_null($this->var);
    }

    public function getVar(): string
    {
        if (\is_null($this->var)) {
            throw new \Exception('Annotation map doesnt contain valid value for var');
        }
        return $this->var;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasParam(string $paramName): bool
    {
        return array_key_exists($paramName, $this->params);
    }

    public function getParam(string $paramName): string
    {
        if (!$this->hasParam($paramName)) {
            throw new \Exception("Annotation map doesnt contain param with name $paramName");
        }
        return $this->params[$paramName];
    }

    public function hasReturn(): bool
    {
        return ! \is_null($this->return);
    }

    public function getReturn(): string
    {
        if (\is_null($this->return)) {
            throw new \Exception('Annotation map doesnt contain valid value for return');
        }
        return $this->return;
    }
}
