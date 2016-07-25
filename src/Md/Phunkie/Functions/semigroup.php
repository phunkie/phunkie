<?php

namespace Md\Phunkie\Functions\semigroup;

use Md\Phunkie\Types\Unit;
use TypeError;

function combine($a, $b) { switch (true) {
    case $a instanceof Unit: return $b;
    case $b instanceof Unit: return $a;
    case gettype($a) != gettype($b) && is_object($a): throw new TypeError("combine is not defined for " . get_class($a));
    case gettype($a) != gettype($b): throw new TypeError("combine is not defined for type " . gettype($a));
    case gettype($a) == gettype($b): switch(gettype($a)) {
        case "int": case "integer": case "double": case "float": return $a + $b;
        case "string": return $a . $b;
        case "bool": case "boolean": return $a && $b;
        case "array": return array_merge($a, $b);
        case "object":
            if (method_exists($a, 'combine')) return $a->combine($b);
            if (is_callable($a)) { return function () use ($a, $b) { return $a($b(...func_get_args())); }; }
            foreach (array_intersect(get_parent_classes($a), get_parent_classes($b)) as $parent)
                if (method_exists($parent, 'combine')) return $a->combine($b);
        break; } }
    throw new TypeError("combining members of different semigroups");
}

function zero($a) { switch (gettype($a)) {
    case "int": case "integer": return 0;
    case "double": case "float": return 0.0;
    case "string": return "";
    case "bool": case "boolean": return true;
    case "array": return [];
    case "object":
        if (method_exists($a, "zero")) return $a->zero();
        if (is_callable($a)) return function($x) { return $x; };
        break; }
    throw new TypeError("zero is not defined for type " . gettype($a));
}

function get_parent_classes($object)
{
    $parents = [];
    $parent = $object;
    while (false !== $parent) {
        $parent = get_parent_class($parent);
        $parents[] = $parent;
    }
    return $parents;
}