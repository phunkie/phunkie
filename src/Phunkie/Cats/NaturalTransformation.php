<?php

namespace Phunkie\Cats;

use function Phunkie\Functions\type\normaliseType;

class NaturalTransformation
{
    use Show;
    private $f;

    public function __construct($f)
    {
        $this->f = $f;
    }

    public function __invoke($a)
    {
        return ($this->f)($a);
    }

    function toString(): string
    {
        $reflection = method_exists($this->f, "__invoke") ?
            new \ReflectionMethod($this->f, "__invoke") :
            new \ReflectionFunction($this->f);
        $from = ltrim(normaliseType($reflection->getParameters()[0]->getType()) ?: "?", "Phunkie\\Types\\");
        $to = ltrim(normaliseType($reflection->getReturnType()) ?: "?", "Phunkie\\Types\\");
        return "~>[$from, $to]";
    }
}
