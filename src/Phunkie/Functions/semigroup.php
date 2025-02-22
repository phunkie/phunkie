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

/**
 * Functions for working with Semigroups.
 * 
 * A Semigroup is a type with an associative binary operation (combine).
 * This allows combining multiple values of the same type into one.
 */

const combine = "\\Phunkie\\Functions\\semigroup\\combine";
 /**
 * Combines two values of the same type.
 * 
 * Uses the type's combine operation to merge values.
 * The operation must be associative: combine(a, combine(b, c)) = combine(combine(a, b), c)
 *
 * Example:
 * ```php
 * // Lists concatenate
 * combine(ImmList(1,2), ImmList(3,4));  // ImmList(1,2,3,4)
 * 
 * // Options take first Some
 * combine(Some(1), None());             // Some(1)
 * combine(None(), Some(2));             // Some(2)
 * 
 * // Numbers add
 * combine(1, 2);                        // 3
 * 
 * // Strings concatenate
 * combine("hello ", "world");           // "hello world"
 * ```
 *
 * @template A
 * @param A $a First value
 * @param A $b Second value
 * @return A Combined result
 */
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
/**
 * Gets the identity element for a type.
 * 
 * Returns the value that, when combined with any other value x,
 * returns x unchanged: combine(zero(), x) = x = combine(x, zero())
 *
 * Example:
 * ```php
 * // Empty list is identity for concatenation
 * zero(ImmList::class);                // ImmList()
 * combine(ImmList(1,2), zero());       // ImmList(1,2)
 * 
 * // None is identity for Options
 * zero(Option::class);                 // None
 * combine(Some(1), zero());           // Some(1)
 * 
 * // 0 is identity for addition
 * zero('integer');                    // 0
 * combine(42, zero());               // 42
 * 
 * // Empty string is identity for concatenation
 * zero('string');                    // ""
 * combine("hello", zero());         // "hello"
 * ```
 *
 * @template A
 * @param class-string<A>|string $type Type to get identity for
 * @return A Identity element
 */
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
