<?php

namespace Md\Phunkie\Types;

use ArrayAccess, SplObjectStorage;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use function Md\Phunkie\Functions\type\promote;

final class ImmMap implements ArrayAccess
{
    use Show;
    private $values;

    public function __construct(...$values)
    {
        $this->values = new SplObjectStorage();
        switch (true) {
            case count($values) == 0: break;
            case count($values) == 1 && is_array($values[0]):
                foreach ($values[0] as $k => $v) {
                    $k = promote($k);
                    $this->values[$k] = $v;
                }
                break;
            default:
                if (count($values) % 2 == 0) {
                    for ($i = 0; $i <= count($values) - 1; $i += 2) {
                        $values[$i] = promote($values[$i]);
                        $this->values[$values[$i]] = $values[$i + 1];
                    }
                    break;
                }
                throw new \Error("not enough arguments for constructor ImmMap");
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

    function toString(): string
    {
        $mappings = [];
        foreach ($this->values as $k) {
            $mappings[] = get_value_to_show($k instanceof ImmString ? $k->get() : $k) . " -> " . get_value_to_show($this->values[$k]);
        }
        return "Map(" . implode(", ", $mappings) . ")";
    }
}