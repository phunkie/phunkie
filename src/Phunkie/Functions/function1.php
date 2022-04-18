<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\function1 {

    use function Phunkie\Functions\semigroup\combine;

    const identity = "\\Phunkie\\Functions\\function1\\identity";
    function identity($x)
    {
        return $x;
    }

    const compose = "\\Phunkie\\Functions\\function1\\compose";
    function compose(callable ...$fs)
    {
        return combine(...array_map(function ($f) {
            return Function1($f);
        }, array_reverse($fs)));
    }

}

namespace {

    use Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
    use Phunkie\Types\Function1;

    function Function1($f)
    {
        if ($f == _) {
            return new WildcardedFunction1();
        }
        return new Function1($f);
    }

}
