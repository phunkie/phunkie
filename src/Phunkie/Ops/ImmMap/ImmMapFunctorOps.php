<?php

namespace Phunkie\Ops\ImmMap;

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\ImmMap;
use Phunkie\Types\Kind;
use Phunkie\Types\Pair;
use SplObjectStorage;
use function Phunkie\Functions\tuple\assign;
use function Phunkie\Functions\type\promote;

trait ImmMapFunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        $mappings = new SplObjectStorage();
        $key = $value = null;
        foreach ($this->copy()->iterator() as $k => $v) {
            (assign($key, $value))($f(Pair($k, $v)));
            $mappings[promote($key)] = $value;
        }
        $map = new ImmMap();
        $map->values = $mappings;
        return $map;
    }

    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }

    public function as($b): Kind
    {
        if ($b->_1 === _) {
            return $this->map(function (Pair $keyValue) use ($b) {
                return Pair($keyValue->_1, $b->_2);
            });
        }
        return ImmMap($b->_1, $b->_2);
    }

    public function void(): Kind
    {
        return $this->map(function (Pair $keyValue) {
            return Pair($keyValue->_1, Unit());
        });
    }

    public function zipWith($f): Kind
    {
        return $this->map(function (Pair $keyValue) use ($f) {
            return Pair($keyValue->_1, Pair($keyValue->_2, $f($keyValue->_2)));
        });
    }
}
