<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Types\Pair;
    use Phunkie\Types\Tuple;
    use Phunkie\Types\Unit;

    /**
     * Creates a tuple from multiple values.
     * 
     * Constructs a tuple containing any number of elements.
     * Tuples are immutable ordered collections of heterogeneous values.
     *
     * Example:
     * ```php
     * Tuple(1, "a", true);      // Tuple(1, "a", true)
     * Tuple("x", [1,2], 3.14);  // Tuple("x", [1,2], 3.14)
     * Tuple();                  // Empty tuple
     * ```
     *
     * @template T1,T2,...
     * @param T1 $value1 First value
     * @param T2 $value2 Second value (optional)
     * @param mixed ...$values Additional values
     * @return Tuple<T1,T2,...> Tuple of values
     */
    function Tuple(...$values): Tuple {
        return match(count($values)) {
            0 => new Unit(),
            2 => new Pair($values[0], $values[1]),
            default => new Tuple(...$values) };
    }

}

namespace Phunkie\Functions\tuple {

    use Phunkie\Types\Tuple;

    const assign = "Md\\Phunkie\\Functions\\tuple\\assign";
    /**
     * Assigns values from a tuple to variables.
     * 
     * Destructures a tuple into individual variables.
     * Similar to list() but works with Tuple objects.
     *
     * Example:
     * ```php
     * $tuple = Tuple("hello", 42);
     * assign($str, $num) = $tuple;
     * echo $str;  // "hello"
     * echo $num;  // 42
     * ```
     *
     * @template T1,T2,...
     * @param T1 &$var1 First variable to assign
     * @param T2 &$var2 Second variable to assign (optional)
     * @param mixed &...$vars Additional variables
     * @return callable(Tuple<T1,T2,...>):void Function that assigns tuple values
     */
    function assign(&$_1, &$_2 = null, &$_3 = null, &$_4 = null, &$_5 = null, &$_6 = null, &$_7 = null, &$_8 = null, &$_9 = null, &$_10 = null, &$_11 = null, &$_12 = null, &$_13 = null, &$_14 = null, &$_15 = null, &$_16 = null, &$_17 = null, &$_18 = null, &$_19 = null, &$_20 = null, &$_21 = null, &$_22 = null, &$_23 = null, &$_24 = null) {
        return function (Tuple $t) use (
            &$_1,
            &$_2,
            &$_3,
            &$_4,
            &$_5,
            &$_6,
            &$_7,
            &$_8,
            &$_9,
            &$_10,
            &$_11,
            &$_12,
            &$_13,
            &$_14,
            &$_15,
            &$_16,
            &$_17,
            &$_18,
            &$_19,
            &$_20,
            &$_21,
            &$_22,
            &$_23,
            &$_24
        ) {
            foreach (range(1, $t->getArity()) as $member) {
                ${"_$member"} = $t->{"_$member"};
            }
        };
    }
}
