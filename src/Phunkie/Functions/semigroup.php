<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\semigroup;

use Phunkie\Types\Unit;
use TypeError;

const combine = "\\Phunkie\\Functions\\semigroup\\combine";
function combine(...$parts)
{
    $getParentClasses = function ($object) {
        $parents = [];
        while (false !== $object) {
            $object = get_parent_class($object);
            if (false === $object) {
                break;
            }
            $parents[] = $object;
        }
        return $parents;
    };

    $combineObjects = function ($a, $b) use ($getParentClasses) {
        if (method_exists($a, 'combine')) {
            return $a->combine($b);
        }
        if (is_callable($a)) {
            return fn () => $a($b(...func_get_args()));
        }
        foreach (array_intersect($getParentClasses($a), $getParentClasses($b)) as $parent) {
            if (method_exists($parent, 'combine')) {
                return $a->combine($b);
            }
        }
    };

    $combine = fn ($a, $b) => match (true) {
        $a instanceof Unit => $b,
        $b instanceof Unit => $a,
        gettype($a) != gettype($b) && is_object($a) => throw new TypeError("cannot combine values of different types. using " . get_class($a)),
        gettype($a) != gettype($b) => throw new TypeError("combine is not defined for type " . gettype($a)),
        gettype($a) == gettype($b) => match (gettype($a)) {
            "int", "integer", "double", "float" => $a + $b,
            "string" => $a . $b,
            "bool", "boolean" => $a && $b,
            "array" => array_merge($a, $b),
            "object" => $combineObjects($a, $b) },
        default => throw new TypeError("combining members of different semigroups") }
    ;

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

const zero = "\\Phunkie\\Functions\\semigroup\\zero";
function zero($a) { return match (gettype($a)) {
    "int", "integer"=> 0,
    "double", "float"=> 0.0,
    "string"=> "",
    "bool", "boolean"=> true,
    "array"=> [],
    "object" =>
        method_exists($a, "zero") ? $a->zero() : (is_callable($a) ? fn ($x) => $x : throw new TypeError("zero is not defined for " . get_class($a))), 
    default => throw new TypeError("zero is not defined for type " . gettype($a)) };
}
