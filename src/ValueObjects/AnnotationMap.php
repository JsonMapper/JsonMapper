<?php

declare(strict_types=1);

namespace JsonMapper\ValueObjects;

class AnnotationMap
{
    private const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

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
        return ! is_null($this->var);
    }

    public function getVar(): string
    {
        if (is_null($this->var)) {
            throw new \Exception('Annotation map doesnt contain valid value for var');
        }
        return $this->var;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasReturn(): bool
    {
        return ! is_null($this->return);
    }

    public function getReturn(): string
    {
        if (is_null($this->return)) {
            throw new \Exception('Annotation map doesnt contain valid value for return');
        }
        return $this->return;
    }

    public static function fromDocBlock(string $docBlock): self
    {
        // Strip away the start "/**' and ending "*/"
        if (strpos($docBlock, '/**') === 0) {
            $docBlock = substr($docBlock, 3);
        }
        if (substr($docBlock, -2) === '*/') {
            $docBlock = substr($docBlock, 0, -2);
        }
        $docBlock = trim($docBlock);

        if (preg_match_all(self::DOC_BLOCK_REGEX, $docBlock, $matches)) {
            for ($x = 0, $max = count($matches[0]); $x < $max; $x++) {
                switch ($matches['name'][$x]) {
                    case 'var':
                        $var = $matches['value'][$x];
                        break;
                    case 'param':
                        $params = $matches['value'];
                        break;
                    case 'return':
                        $return = $matches['value'][$x];
                        break;
                }
            }
        }

        return new self($var ?? null, $params ?? [], $return ?? null);
    }
}
