<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

use BadMethodCallException;
use Phunkie\Cats\Applicative;
use Phunkie\Cats\Show;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\type\promote;
use Phunkie\Ops\ImmSet\ImmSetFunctorOps;
use Phunkie\Utils\Iterator;
use TypeError;

class ImmSet implements Kind, Applicative
{
    use Show, ImmSetFunctorOps;
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

    public function toString(): string
    {
        return "Set(" . implode(", ", array_map(function($e) { return showValue($e); }, $this->elements)) . ")";
    }

    public function iterator()
    {
        $storage = new \SplObjectStorage();
        foreach ($this->toArray() as $k => $v) {
            $storage[promote($k)] = $v;
        }
        return new Iterator($storage);
    }

    public function union(ImmSet $set)
    {
        return ImmSet(...array_merge($this->elements, $set->elements));
    }

    public function intersect(ImmSet $set)
    {
        $new = [];
        foreach ($this->elements as $element) {
            if ($set->contains($element)) {
                $new[] = $element;
            }
        }
        return ImmSet(...$new);
    }

    public function diff(ImmSet $set)
    {
        $new = [];
        foreach ($this->elements as $element) {
            if (!$set->contains($element)) {
                $new[] = $element;
            }
        }
        foreach ($set->elements as $element) {
            if (!$this->contains($element)) {
                $new[] = $element;
            }
        }
        return ImmSet(...$new);
    }

    public function apply(Kind $f): Kind
    {
        $apply = function () use ($f) {
            $result = [];
            foreach($this->toArray() as $a) {
                foreach ($f->toArray() as $ff) {
                    if (!is_callable($ff)) {
                        throw new TypeError(sprintf("`apply` takes Set<callable>, Set<%s> given", gettype($ff)));
                    }
                    $result[] = call_user_func($ff, $a);
                }
            }
            return ImmSet(...$result);
        };

        switch (true) {
            case $f == None(): return None();
            case $f instanceof ImmSet: return $apply();
            case $f instanceof Function1 && is_callable($f->get()):
                return $this->map($f->get());
            default: throw new BadMethodCallException();
        }
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function($b) use ($f) { return function($a) use ($f, $b) { return $f($a, $b);};}));
    }

    public function pure($a): Kind
    {
        return ImmSet($a);
    }

    public function isEmpty(): bool
    {
        return [] === $this->elements;
    }
}