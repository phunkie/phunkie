<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;

class ImmSet
{
    use Show;
    private $elements = [];

    public function __construct(...$elements)
    {
        foreach ($elements as $element) {
            if (!$this->contains($element)) {
                $this->elements[] = $element;
            }
        }
    }

    public function contains($element)
    {
        return ((!is_object($element) && in_array($element, $this->elements, true)) ||
            (is_object($element) && in_array($element, $this->elements)));
    }

    public function minus($element)
    {
        if (!$this->contains($element)) {
            return ImmSet(...$this->elements);
        }

        $elements = [];
        foreach ($this->elements as $el) {
            if ((is_object($el) && $element == $el) || (!is_object($el) && $element === $el)) continue;
            $elements[] = $el;
        }
        return ImmSet(...$elements);
    }

    public function plus($element)
    {
        if ($this->contains($element)) {
            return ImmSet(...$this->elements);
        }

        return ImmSet(...array_merge($this->elements, [$element]));
    }

    public function toArray()
    {
        return $this->elements;
    }

    function toString(): string
    {
        return "Set(" . implode(", ", array_map(function($e) { return get_value_to_show($e); }, $this->elements)) . ")";
    }
}