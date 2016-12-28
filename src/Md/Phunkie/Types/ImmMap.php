<?php

namespace Md\Phunkie\Types;

use ArrayAccess, SplObjectStorage;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use function Md\Phunkie\Functions\type\promote;
use Md\Phunkie\Ops\ImmMap\ImmMapEqOps;
use Md\Phunkie\Utils\Iterator;

final class ImmMap implements ArrayAccess
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
            $mappings[] = get_value_to_show($k instanceof ImmString || $k instanceof ImmInteger ? $k->get() : $k) . " -> " . get_value_to_show($this->values[$k]);
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