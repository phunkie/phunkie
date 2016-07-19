<?php

namespace Md\Phunkie\Functions\semigroup;

use Md\Phunkie\Types\Unit;
use TypeError;

function combine($a, $b) {
    switch(true) {
        case ($a instanceof Unit):
            return $b;
        case ($b instanceof Unit):
            return $a;
        case (gettype($a) == gettype($b)):
            switch (gettype($a)) {
                case "int":
                case "integer":
                case "double":
                case "float":
                    return $a + $b;
                case "string":
                    return $a . $b;
                case "bool":
                case "boolean":
                    return $a && $b;
                case "array":
                    return array_merge($a, $b);
                case "object":
                    if (is_callable($a)) {
                        if (method_exists($a, "combine")) {
                            return $a->combine($b);
                        }
                        return function () use ($a, $b) {
                            return $a($b(...func_get_args()));
                        };
                    }
                    if (get_class($a) == get_class($b)) {
                        if (method_exists($a, 'combine')) {
                            return $a->combine($b);
                        }
                    }
                    $aParents = [];
                    $parent = $a;
                    while (false !== $parent) {
                        $parent = get_parent_class($parent);
                        $aParents[] = $parent;
                    }
                    $bParents = [];
                    $parent = $b;
                    while (false !== $parent) {
                        $parent = get_parent_class($parent);
                        $bParents[] = $parent;
                    }
                    foreach (array_intersect($aParents, $bParents) as $parent) {
                        if (method_exists($parent, 'combine')) {
                            return $a->combine($b);
                        }
                    }
                    break;
            }
            break;
        default:
            throw new TypeError("combining members of different semigroups");
            break;
    }
    if (is_object($a)) {
        throw new TypeError("combine is not defined for " . get_class($a));
    }
    throw new TypeError("combine is not defined for type " . gettype($a));
}

function zero($a) {
    switch(gettype($a)) {
        case "int":
        case "integer":
            return 0;
        case "double":
        case "float":
            return 0.0;
        case "string":
            return "";
        case "bool":
        case "boolean":
            return true;
        case "array":
            return [];
        case "object":
            if (is_callable($a)) {
                if (method_exists($a, "zero")) {
                    return $a->zero();
                }
                $identity = function($x) { return $x; };
                return $identity;
            }
            if (method_exists($a, "zero")) {
                return $a->zero();
            }
    }
    throw new TypeError("zero is not defined for type " . gettype($a));
}