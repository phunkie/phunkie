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

    function Tuple(...$values) { return match(count($values)) {
        0 => new Unit(),
        2 => new Pair($values[0], $values[1]),
        default => new Tuple(...$values) };
    }

}

namespace Phunkie\Functions\tuple {

    use Phunkie\Types\Tuple;

    const assign = "Md\\Phunkie\\Functions\\tuple\\assign";
    function assign(&$_1, &$_2 = null, &$_3 = null, &$_4 = null, &$_5 = null, &$_6 = null, &$_7 = null, &$_8 = null, &$_9 = null, &$_10 = null, &$_11 = null, &$_12 = null, &$_13 = null, &$_14 = null, &$_15 = null, &$_16 = null, &$_17 = null, &$_18 = null, &$_19 = null, &$_20 = null, &$_21 = null, &$_22 = null, &$_23 = null, &$_24 = null)
    {
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
