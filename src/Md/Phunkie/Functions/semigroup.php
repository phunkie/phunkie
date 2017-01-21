<?php

namespace Md\Phunkie\Functions\semigroup;

use Md\Phunkie\Types\Unit;
use TypeError;

const combine = "\\Md\\Phunkie\\Functions\\semigroup\\combine";
function combine(...$parts) {
    $getParentClasses = function($object) {
        $parents = [];
        while (false !== $object) {
            $object = get_parent_class($object);
            if (false === $object) break;
            $parents[] = $object;
        }
        return $parents;
    };

    $combine = function($a, $b) use ($getParentClasses) { switch (true) {
        case $a instanceof Unit: return $b;
        case $b instanceof Unit: return $a;
        case gettype($a) != gettype($b) && is_object($a): throw new TypeError("cannot combine values of different types. using " . get_class($a));
        case gettype($a) != gettype($b): throw new TypeError("combine is not defined for type " . gettype($a));
        case gettype($a) == gettype($b): switch(gettype($a)) {
            case "int": case "integer": case "double": case "float": return $a + $b;
            case "string": return $a . $b;
            case "bool": case "boolean": return $a && $b;
            case "array": return array_merge($a, $b);
            case "object":
                if (method_exists($a, 'combine')) return $a->combine($b);
                if (is_callable($a)) { return function () use ($a, $b) { return $a($b(...func_get_args())); }; }
                foreach (array_intersect($getParentClasses($a), $getParentClasses($b)) as $parent)
                    if (method_exists($parent, 'combine')) return $a->combine($b);
                break; } }
        throw new TypeError("combining members of different semigroups");
    };

    if (func_num_args() == 0) {
        return Unit();
    } elseif (func_num_args() == 1) {
        return $parts[0];
    } elseif (func_num_args() == 2) {
        return $combine($parts[0], $parts[1]);
    } else {
        return $combine($parts[0], combine($parts[1], ...array_slice($parts, 2)));
    }
}

const zero = "\\Md\\Phunkie\\Functions\\semigroup\\zero";
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