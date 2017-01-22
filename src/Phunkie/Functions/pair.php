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

    function Pair(...$args)
    {
        return new Pair(...$args);
    }
}

namespace Phunkie\Functions\pair {

    use Phunkie\Types\Pair;

    const _1 = "\\Phunkie\\Functions\\pair\\_1";
    function _1(Pair $pair)
    {
        return $pair->_1;
    }

    const _2 = "\\Phunkie\\Functions\\pair\\_2";
    function _2(Pair $pair)
    {
        return $pair->_2;
    }
}