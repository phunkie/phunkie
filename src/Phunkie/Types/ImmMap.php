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

use ArrayAccess, SplObjectStorage;
use Phunkie\Cats\Show;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\type\promote;
use Phunkie\Ops\ImmMap\ImmMapEqOps;
use Phunkie\Utils\Copiable;
use Phunkie\Utils\Iterator;

final class ImmMap implements ArrayAccess, Copiable
{
    use Show, ImmMapEqOps;
    private $values;

    public function __construct(...$values)
    {
        $this->values = new SplObjectStorage();
        switch (true) {
            case $this->noArguments($values): break;
            case $this->isArrayAndOneArgument($values): $this->createFromArray($values); break;
            case $this->oddNumberOfArguments($values): throw new \Error("not enough arguments for constructor ImmMap");
            default: $this->createFromVariadic($values);
        }
    }

    public function offsetExists($offset)
    {
        foreach ($this->values as $k) {
            if ($k == promote($offset)) {
                return true;
            }
        }
        return false;
    }

    public function offsetGet($offset)
    {
        foreach ($this->values as $k) {
            if ($k == promote($offset)) {
                return Some($this->values[$k]);
            }
        }
        return None();
    }

    public function offsetSet($offset, $value)
    {
        throw new \TypeError("ImmMaps are immutable");
    }

    public function offsetUnset($offset)
    {
        throw new \TypeError("ImmMaps are immutable");
    }

    public function copy(array $fields = [])
    {
        $copy = ImmMap();
        $copy->values = clone $this->values;
        foreach ($fields as $field => $value) {
            $copy = $copy->minus($field);
            $copy->values->attach(promote($field), $value);
        }
        return $copy;
    }

    public function get($offset)
    {
        return $this->offsetGet($offset);
    }

    public function contains($offset)
    {
        return $this->offsetExists($offset);
    }

    public function getOrElse($offset, $default)
    {
        return $this->get($offset)->getOrElse($default);
    }

    public function keys()
    {
        $keys = [];
        foreach ($this->values as $k) {
            $keys[] = $k instanceof ImmString || $k instanceof ImmInteger ? $k->get() : $k;
        }
        return $keys;
    }

    public function values()
    {
        $values = [];
        foreach ($this->values as $k) {
            $values[] = $this->values[$k];
        }
        return $values;
    }

    public function toString(): string
    {
        $mappings = [];
        foreach ($this->values as $k) {
            $mappings[] = showValue($k instanceof ImmString || $k instanceof ImmInteger ? $k->get() : $k) . " -> " . showValue($this->values[$k]);
        }
        return "Map(" . implode(", ", $mappings) . ")";
    }

    public function plus($k, $v)
    {
        $mappings = clone $this->values;
        $mappings->attach(promote($k), $v);
        $map = new self();
        $map->values = $mappings;
        return $map;
    }

    public function minus($k)
    {
        $mappings = new SplObjectStorage();
        if ($this->contains($k)) {
            foreach ($this->values as $offset) {
                if (promote($k) != $offset) {
                    $mappings[$offset] = $this->values[$offset];
                }
            }
            $map = new self();
            $map->values = $mappings;
            return $map;
        }
        return clone ($this);
    }

    public function iterator()
    {
        return new Iterator($this->values);
    }

    private function createFromArray($values)
    {
        foreach ($values[0] as $k => $v) {
            $k = promote($k);
            $this->values[$k] = $v;
        }
    }

    private function createFromVariadic($values)
    {
        for ($i = 0; $i <= count($values) - 1; $i += 2) {
            $values[$i] = promote($values[$i]);
            $this->values[$values[$i]] = $values[$i + 1];
        }
    }

    private function isArrayAndOneArgument($values)
    {
        return count($values) == 1 && is_array($values[0]);
    }

    private function noArguments($values)
    {
        return count($values) == 0;
    }

    private function oddNumberOfArguments($values)
    {
        return count($values) % 2 != 0;
    }
}