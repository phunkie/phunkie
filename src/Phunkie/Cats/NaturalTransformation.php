<?php

namespace Phunkie\Cats;

use function Phunkie\Functions\type\normaliseType;

class NaturalTransformation
{
    use Show;
    private $f;
    private $from;
    private $to;

    public function __construct(callable $f)
    {
        $this->f = $f;
        $this->extractToAndFromTypes();
    }

    public function __invoke($a)
    {
        return ($this->f)($a);
    }

    public function toString(): string
    {
        return "NaturalTransformation" . "@" . substr(ltrim(spl_object_hash($this), "0"), 0, 8);
    }

    public function showType()
    {
        return "~>[$this->from, $this->to]";
    }

    public function __get($member)
    {
        switch ($member) {
            case "from": return $this->from;
            case "to": return $this->to;
        }
        throw new \Error("$member is not a member of Natural Transformation");
    }

    public function __set($member, $value)
    {
        throw new \Error("Natural Transformation is immutable");
    }

    private function extractToAndFromTypes()
    {
        $reflection = method_exists($this->f, "__invoke") ?
            new \ReflectionMethod($this->f, "__invoke") :
            new \ReflectionFunction($this->f);
        $this->from = ltrim(normaliseType($reflection->getParameters()[0]->getType()) ?: "?", "Phunkie\\Types\\");
        $this->to = ltrim(normaliseType($reflection->getReturnType()) ?: "?", "Phunkie\\Types\\");
    }
}
